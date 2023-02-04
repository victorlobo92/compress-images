<?php

namespace App\Services\Compress;

use Exception;

abstract class Compress implements CompressInterface
{
    protected $file;
    private string $file_destination_folder;
    protected string $quality;

    public function setup(array $file_to_compress, string $file_destination_folder)
    {
        $this->file = $file_to_compress;
        $this->file_destination_folder = $file_destination_folder;

        $this->validate();

        return $this;
    }

    protected function validate()
    {
        if(empty($this->quality)){
            throw new Exception('Please inform the quality!');
        }
    }
}