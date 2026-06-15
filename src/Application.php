<?php
declare(strict_types=1);

namespace PHPDel;

use League\CLImate\CLImate;
use League\CLImate\TerminalObject\Dynamic\Radio;
use PHPDel\Comment\CommentPatternProvider;
use PHPDel\Factory\ConfigFactory;
use PHPDel\Flag\FlagList;
use PHPDel\Validation\ValidationRunner;

class Application
{
    public function __construct(private readonly CLImate $cli)
    {
        $this->initCli();
    }

    private function initCli(): void
    {
        $this->cli->arguments->add([
            'flag' => [
                'longPrefix'  => 'flag',
                'description' => 'Specify the flag to delete without the interactive prompt',
            ],
            'list-flags' => [
                'longPrefix'  => 'list-flags',
                'description' => 'List detected flags and exit without deleting',
                'noValue'     => true,
            ],
            'dry-run' => [
                'longPrefix'  => 'dry-run',
                'description' => 'Make it work without executing delete',
                'noValue'     => true,
            ],
            'validate' => [
                'longPrefix'  => 'validate',
                'description' => 'Validate all php-del markers without modifying files',
                'noValue'     => true,
            ],
            'help' => [
                'longPrefix'  => 'help',
                'description' => 'Prints a usage statement',
                'noValue'     => true,
            ],
        ]);
        $this->cli->arguments->parse();
    }

    private function help(): bool
    {
        return (bool) $this->cli->arguments->get('help');
    }

    private function isDryRun(): bool
    {
        return (bool) $this->cli->arguments->get('dry-run');
    }

    private function isListFlags(): bool
    {
        return (bool) $this->cli->arguments->get('list-flags');
    }

    private function isValidate(): bool
    {
        return (bool) $this->cli->arguments->get('validate');
    }

    private function selectedFlag(): ?string
    {
        $flag = $this->cli->arguments->get('flag');

        if (!is_string($flag) || $flag === '') {
            return null;
        }

        return $flag;
    }

    private function hasSelectedFlagArgument(): bool
    {
        return $this->cli->arguments->defined('flag');
    }

    private function isNonInteractive(): bool
    {
        return $this->isListFlags() || $this->hasSelectedFlagArgument() || $this->isValidate();
    }

    public function main(): int
    {
        if ($this->help()) {
            $this->cli->usage();
            return 0;
        }

        if ($this->isValidate()) {
            return $this->validate();
        }

        $config = ConfigFactory::make();

        if (!$this->isNonInteractive()) {
            $this->cli->blink()->dim('Finding flag...');
        }
        $finder = new Finder($config);
        $finder->findFlag();
        $flagList = $finder->getFlagList();

        if ($this->isListFlags()) {
            if ($flagList->empty()) {
                $this->cli->backgroundYellow()->out("Nothing flag.");
                return 0;
            }

            foreach ($flagList as $flag) {
                $this->cli->out((string) $flag);
            }
            return 0;
        }

        if (!$this->hasSelectedFlagArgument() && $flagList->empty()) {
            $this->cli->backgroundYellow()->out("Nothing flag.");
            return 0;
        }

        $deleteFlag = $this->resolveFlag($flagList);

        if ($deleteFlag === null) {
            return 1;
        }

        foreach ($finder->getTargetFileList() as $file) {
            try {
                $text = $this->readFile($file);
                $deleter = new Deleter($text);

                if ($deleter->delete($file, $deleteFlag, $this->isDryRun())) {
                    $this->cli->backgroundGreen($file . "(delete)");
                    continue;
                }

                $rewriter = new Rewriter($text);
                $commentPattern = (new CommentPatternProvider($file))->get($deleteFlag);
                $text = $rewriter->exec($commentPattern);

                if ($rewriter->count() === 0) {
                    continue;
                }

                if (!$this->isDryRun()) {
                    $result = file_put_contents($file, $text);

                    if ($result === false) {
                        throw new \RuntimeException("File Put Contents Error.");
                    }
                }
                $this->cli->backgroundGreen($file . "({$rewriter->count()})");
            } catch (\Throwable $throwable) {
                $this->cli->backgroundRed($file);
                $this->cli->error("[ERROR] " . $throwable->getMessage());
            }
        }
        $this->cli->out("End php-del");

        return 0;
    }

    private function validate(): int
    {
        if ($this->hasSelectedFlagArgument() || $this->isListFlags() || $this->isDryRun()) {
            $this->cli->error(
                '[ERROR] The --validate option cannot be combined with --flag, --list-flags, or --dry-run.'
            );
            return 2;
        }

        try {
            $result = (new ValidationRunner(ConfigFactory::make()))->run();
        } catch (\Throwable $throwable) {
            $this->cli->error('[ERROR] ' . $throwable->getMessage());
            return 2;
        }

        foreach ($result->diagnostics as $diagnostic) {
            $this->cli->error((string) $diagnostic);
        }

        if ($result->diagnostics === []) {
            $this->cli->out(
                "php-del validation passed: {$result->fileCount} files, {$result->markerCount} markers"
            );
            return 0;
        }

        $errorCount = count($result->diagnostics);
        $errorFileCount = $result->errorFileCount();
        $this->cli->out(
            "php-del validation failed: {$errorCount} errors in {$errorFileCount} files"
        );

        return $result->exitCode();
    }

    private function resolveFlag(FlagList $flagList): ?string
    {
        $selected = $this->selectedFlag();

        if ($this->hasSelectedFlagArgument()) {
            if ($selected === null) {
                $this->cli->error("[ERROR] The --flag option requires a value.");
                return null;
            }

            if (!$flagList->has($selected)) {
                $this->cli->error("[ERROR] The specified flag does not exist: {$selected}");
                return null;
            }

            return $selected;
        }

        $input = $this->cli->radio('Please choice me one of the following flag:', (array) $flagList);

        if (!$input instanceof Radio) {
            throw new \RuntimeException('Unable to create flag selection prompt.');
        }

        return $input->prompt();
    }

    private function readFile(string $file): string
    {
        $text = file_get_contents($file);

        if ($text === false) {
            throw new \RuntimeException("File Get Contents Error.");
        }

        return $text;
    }
}
