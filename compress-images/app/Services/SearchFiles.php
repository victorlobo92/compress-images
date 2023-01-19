<?php

namespace App\Services;

class SearchFiles {

    public function __construct()
    {
        $this->validateEnvironmentVariables();
    }

    private function validateEnvironmentVariables()
    {
        if(empty(getenv('SEARCH_FILE_FOLDER'))) {
            throw new \Exception('Envirionment variable SEARCH_FILE_FOLDER is missing!');
        }

        if(empty(getenv('COMPRESSED_FILES_FOLDER'))) {
            throw new \Exception('Envirionment variable COMPRESSED_FILES_FOLDER is missing!');
        }
    }
}