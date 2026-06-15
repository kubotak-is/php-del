<?php
declare(strict_types=1);

namespace PHPDel;

readonly class Config
{
    private array $dirs;
    private array $extensions;

    public function __construct(array $config)
    {
        $this->validate($config);
        $this->dirs = $config['dirs'];
        $this->extensions = $config['extensions'] ?? ['php'];
    }

    private function validate(array $config): void
    {
        if (!isset($config['dirs']) || !is_array($config['dirs'])) {
            throw new \InvalidArgumentException('The "dirs" configuration must be an array.');
        }

        if (isset($config['extensions']) && !is_array($config['extensions'])) {
            throw new \InvalidArgumentException('The "extensions" configuration must be an array.');
        }
    }

    public function getDirs(): array
    {
        return $this->dirs;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
