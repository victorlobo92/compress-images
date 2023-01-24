<?php

namespace App\Services;

interface CompressFilesInterface
{
    /**
     * Constructor receives array list of files to be compressed
     *
     * @param array $files_to_compress
     */
    public function __construct(array $files_to_compress);

    /**
     * Compress files and returns an array list
     *
     * @return array
     */
    public function compress_files(): array;
}