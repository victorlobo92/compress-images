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

    public function compress()
    {
        $this->make_dir_if_needed($this->get_file_destination_folder());

        return $this->get_file_destination_path();
    }

    /**
     * Make directory if it does not exist yet
     *
     * @param string $dir
     * @return void
     */
    private function make_dir_if_needed($dir)
    {
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function get_file_destination_folder()
    {
        $compress_folder_path = trim(getenv('COMPRESS_FOLDER_PATH'), '/');

        if (!$compress_folder_path) $compress_folder_path = base_path();

        $search_files_folder = $compress_folder_path . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/') . '/';
        $destination_folder = str_replace($search_files_folder, '', $this->file['folder']);

        return $this->file_destination_folder . $destination_folder;
    }

    protected function get_file_destination_path()
    {
        return $this->get_file_destination_folder() . $this->file['name'];
    }
}