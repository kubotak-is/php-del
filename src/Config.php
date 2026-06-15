<?php
declare(strict_types=1);

namespace PHPDel;

readonly class Config
{
    /** @var list<string> */
    private array $dirs;

    /** @var list<string> */
    private array $extensions;

    /**
     * @param array<array-key, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->dirs = $this->stringList($config['dirs'] ?? null, 'dirs');
        $this->extensions = array_key_exists('extensions', $config)
            ? $this->stringList($config['extensions'], 'extensions')
            : ['php'];
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value, string $name): array
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException("The \"{$name}\" configuration must be an array.");
        }

        $values = [];

        foreach ($value as $item) {
            if (!is_string($item)) {
                throw new \InvalidArgumentException(
                    "The \"{$name}\" configuration must contain only strings."
                );
            }

            $values[] = $item;
        }

        return $values;
    }

    /**
     * @return list<string>
     */
    public function getDirs(): array
    {
        return $this->dirs;
    }

    /**
     * @return list<string>
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
