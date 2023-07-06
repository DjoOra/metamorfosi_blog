<?php

namespace App;

class FileHandler
{
    private $file;

    public function __construct($filename)
    {
        $this->file = $filename;
    }

    public function load(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }
        $data = file_get_contents($this->file);
        return json_decode($data, true);
    }

    public function save(array $data): void
    {
        $data = json_encode($data);
        file_put_contents($this->file, $data);
    }
}
