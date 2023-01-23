<?php

namespace App\Services;

use App\Exceptions\MissingEnvironmentVariableException;
class SearchFiles {

    public function __construct()
    {
        $this->validateEnvironmentVariables();
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
}