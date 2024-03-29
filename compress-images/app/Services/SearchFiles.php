<?php

namespace App\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
use Exception;

class SearchFiles {

    private $compress_folder_path;
    private $search_files_folder;

    public function __construct()
    {
        $this->setup();
        $this->validate();
    }

    private function setup()
    {
        $compress_folder_path = trim(getenv('COMPRESS_FOLDER_PATH'), '/');

        $this->compress_folder_path = $compress_folder_path ? $compress_folder_path : base_path();
        $this->search_files_folder = trim(getenv('SEARCH_FILES_FOLDER'), '/');
    }

    private function get_search_files_folder()
    {
        return $this->compress_folder_path . '/' . $this->search_files_folder;
    }

    private function validate()
    {
        $this->validate_environment_variables();
        $this->validate_folders_exist();
        $this->validate_folders_access_rights();
    }

    private function validate_environment_variables()
    {
        if(empty(getenv('SEARCH_FILES_FOLDER'))) {
            throw new MissingEnvironmentVariableException('Envirionment variable SEARCH_FILES_FOLDER is missing!');
        }
    }

    private function validate_folders_exist()
    {
        if(!is_dir($this->get_search_files_folder())) {
            throw new FileOrFolderNotFoundException('The folder ' . $this->get_search_files_folder() . ' was not found!');
        }
    }

    private function validate_folders_access_rights()
    {
        if(!is_readable($this->get_search_files_folder())) {
            throw new AccessRightsException('The application does not have reading access rights to ' . $this->get_search_files_folder() . ' folder!');
        }
    }

    /**
     * Find files to compress
     * @return array list of files paths to be compressed
     */
    public function find($dir = null)
    {
        $dir = $dir ?? $this->get_search_files_folder();
        $dir = trim($dir) . '/';

        $to_compress = [];
        if (is_dir($dir)) {
            try {
                $dir_handler = opendir($dir);
            }
            catch(Exception $e) {
                $dir_handler = false;
            }

            if ($dir_handler) {
                while (($file = readdir($dir_handler)) !== false) {
                    switch (filetype($dir . $file)) {
                        case 'file':
                            $to_compress[] = [
                                'folder' => $dir,
                                'name' => $file
                            ];
                        break;
                        case 'dir':
                            if($file === '.' || $file === '..') continue 2;
    
                            $to_compress = array_merge($to_compress, $this->find($dir . $file));
                        break;
                    }
                }
                closedir($dir_handler);
            }
        }

        return $to_compress;
    }
}