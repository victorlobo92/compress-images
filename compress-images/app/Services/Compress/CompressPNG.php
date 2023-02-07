<?php

namespace App\Services\Compress;

class CompressPNG extends Compress
{
    public function __construct()
    {
        $this->quality = (!empty($this->quality)) ? $this->quality : 9 ;
    }

    /**
     * Compress single PNG file
     *
     */
    protected function compress_file($temp_file_name)
    {
        $file_compressed = imagecreatefrompng($this->file['path']);
        imagealphablending($file_compressed , false);
        imagesavealpha($file_compressed , true);
        imagepng($file_compressed, $temp_file_name, $this->quality);

        return $file_compressed;
    }
}