<?php

namespace App\Services;

class CompressFiles
{
    private $compress_folder_path;
    private string $compressed_files_folder;
    private array $files_to_compress;
    private array $files_compressed = [];

    public function __construct(array $files_to_compress)
    {
        $this->files_to_compress = $files_to_compress;
        $this->setup();
    }

    private function setup()
    {
        $compress_folder_path = trim(getenv('COMPRESS_FOLDER_PATH'), '/');

        $this->compress_folder_path = ($compress_folder_path ? $compress_folder_path : base_path()) . '/';
        $this->compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';
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