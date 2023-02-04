<?php

namespace App\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
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
        $this->validate();
    }

    private function setup()
    {
        $compress_folder_path = trim(getenv('COMPRESS_FOLDER_PATH'), '/');

        $this->compress_folder_path = ($compress_folder_path ? $compress_folder_path : base_path()) . '/';
        $this->compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';
    }

    private function validate()
    {
        $this->validate_environment_variables();
        $this->validate_folders_exist();
        $this->validate_folders_access_rights();
    }

    private function validate_environment_variables()
    {
        if (empty(trim(getenv('COMPRESSED_FILES_FOLDER'), '/'))) {
            throw new MissingEnvironmentVariableException('Envirionment variable COMPRESSED_FILES_FOLDER is missing or invalid!');
        }
    }

    private function validate_folders_exist()
    {
        if (!is_dir($this->get_compressed_files_path())) {
            throw new FileOrFolderNotFoundException('The folder ' . $this->get_compressed_files_path() . ' was not found!');
        }
    }

    private function validate_folders_access_rights()
    {
        if (!is_writable($this->get_compressed_files_path())) {
            throw new AccessRightsException('The application does not have writing access rights to ' . $this->get_compressed_files_path() . ' folder!');
        }
    }

    private function get_compressed_files_path()
    {
        return $this->compress_folder_path . $this->compressed_files_folder;
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