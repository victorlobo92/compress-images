<?php

namespace App\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
use App\Services\Compress\CompressInterface;
use Exception;

class CompressFiles
{
    const ARRAY_IMG_MIME_TYPES = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif', 'image/webp');

    private CompressInterface $compressPNG;
    private $compress_folder_path;
    private string $compressed_files_folder;
    private array $files_to_compress;
    private array $files_compressed = [];

    public function __construct(CompressInterface $compressPNG, array $files_to_compress)
    {
        $this->compressPNG = $compressPNG;
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

    private function get_max_size()
    {
        $max_file_size_mb = getenv('MAX_FILE_SIZE_MB') ?: 10;

        return $max_file_size_mb * 1024 * 1024;
    }

    /**
     * Compress files
     *
     * @return void
     */
    public function compress(): array
    {
        foreach ($this->files_to_compress as $file) {
            $error = $this->validate_file($file);

            if (!is_null($error)) throw new Exception($error);

            switch ($file['mime']) {
                case 'image/png':
                case 'image/x-png':
                    $this->files_compressed[] = $this->compressPNG
                        ->setup($file, $this->get_compressed_files_path())
                        ->compress();
                    break;
            }
        }

        return $this->files_compressed;
    }

    private function validate_file(&$file)
    {
        $file['path'] = $file['folder'] . $file['name'];

        try {
            if (empty($file['path']) || !file_exists($file['path'])) {
                throw new Exception("The image on path '{$file['path']}' is missing!");
            }

            $image_data = getimagesize($file['path']);

            if (!$image_data || !in_array($image_data['mime'], self::ARRAY_IMG_MIME_TYPES)) {
                throw new Exception("The image '{$file['path']}' is not supported!");
            }
            
            $file['mime'] = $image_data['mime'];
            $file['size'] = filesize($file['path']);

            if ($file['size'] >= $this->get_max_size()) {
                $max_size_str = $this->get_max_size() / 1024 / 1024;

                throw new Exception("Please send a imagem smaller than {$max_size_str}mb!");
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return null;
    }
}