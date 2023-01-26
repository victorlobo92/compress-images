<?php

namespace App\Services\Compress;

interface CompressInterface
{
    /**
     * Setup receives array list of files to be compressed and file destination folder
     *
     * @param array $file_to_compress file to be compressed data
     * @param string $file_destination_folder folder where to save the compressed file
     * @return self
     */
    function setup(array $file_to_compress, string $file_destination_folder);

    /**
     * Compress files and returns an array list
     *
     * @return array
     */

    function compress();
}