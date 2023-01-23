<?php

namespace App\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
class SearchFiles {

    private $compress_folder_path;
    private $search_files_folder;
    private $compressed_files_folder;

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
        $this->compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/');
    }

    private function get_search_files_folder()
    {
        return $this->compress_folder_path . '/' . $this->search_files_folder;
    }

    private function get_compressed_files_folder()
    {
        return $this->compress_folder_path . '/' . $this->compressed_files_folder;
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

        if(empty(getenv('COMPRESSED_FILES_FOLDER'))) {
            throw new MissingEnvironmentVariableException('Envirionment variable COMPRESSED_FILES_FOLDER is missing!');
        }
    }

    private function validate_folders_exist()
    {
        if(!is_dir($this->get_search_files_folder())) {
            throw new FileOrFolderNotFoundException('The folder ' . $this->get_search_files_folder() . ' was not found!');
        }

        if(!is_dir($this->get_compressed_files_folder())) {
            throw new FileOrFolderNotFoundException('The folder ' . $this->get_compressed_files_folder() . ' was not found!');
        }
    }

    private function validate_folders_access_rights()
    {
        if(!is_readable($this->get_search_files_folder())) {
            throw new AccessRightsException('The application does not have reading access rights to ' . $this->get_search_files_folder() . ' folder!');
        }

        if(!is_writable($this->get_compressed_files_folder())) {
            throw new AccessRightsException('The application does not have writing access rights to ' . $this->get_compressed_files_folder() . ' folder!');
        }
    }
}