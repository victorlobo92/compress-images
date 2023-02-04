<?php

namespace App\Services;

class CompressFiles
{
    private array $files_to_compress;
    private array $files_compressed = [];

    public function __construct(array $files_to_compress)
    {
        $this->files_to_compress = $files_to_compress;
    }

    /**
     * Compress files
     *
     * @return void
     */
    public function compress_files(): array
    {
        return $this->files_compressed;
    }
}