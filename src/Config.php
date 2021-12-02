<?php
declare(strict_types=1);

namespace PHPDel;

class Config
{
    private array $dirs = [];
    private array $extensions = [];

    public function __construct(array $config)
    {
        $this->validate($config);
        $this->dirs = $config['dirs'];
        $this->extensions = $config['extensions'] ?? ['php'];
    }

    private function validate(array $config): void
    {
        if (!isset($config['dirs'])) {
            throw new \InvalidArgumentException("");
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
