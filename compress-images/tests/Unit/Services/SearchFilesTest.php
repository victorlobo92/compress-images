<?php

namespace Tests\Unit\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
use Tests\TestCase;
use App\Services\SearchFiles;
use Exception;

class SearchFilesTest extends TestCase
{
    /**
     * @after
     */
    public function tearDownEnvironmentChanges()
    {
        putenv("SEARCH_FILES_FOLDER=" . env('SEARCH_FILES_FOLDER'));
        putenv("COMPRESSED_FILES_FOLDER=" . env('COMPRESSED_FILES_FOLDER'));
    }

    /**
     * Test if missing environment variable SEARCH_FILES_FOLDER throws exception
     *
     * @return void
     */
    public function test_missing_search_files_folder_env()
    {
        $this->expectException(MissingEnvironmentVariableException::class);

        putenv("SEARCH_FILES_FOLDER=");

        new SearchFiles();
    }

    /**
     * Test if missing environment variable COMPRESSED_FILES_FOLDER throws exception
     *
     * @return void
     */
    public function test_missing_compressed_files_folder_env()
    {
        $this->expectException(MissingEnvironmentVariableException::class);

        putenv("COMPRESSED_FILES_FOLDER=");
        
        new SearchFiles();
    }

    /**
     * Test if exception is thrown when SEARCH_FILES_FOLDER directory does not exists
     *
     * @return void
     */
    public function test_search_folder_exists_exception()
    {
        $this->expectException(FileOrFolderNotFoundException::class);

        putenv("SEARCH_FILES_FOLDER=fake_folder");
        
        new SearchFiles();
    }

    /**
     * Test if exception is thrown when COMPRESSED_FILES_FOLDER directory does not exists
     *
     * @return void
     */
    public function test_compressed_folder_exists_exception()
    {
        $this->expectException(FileOrFolderNotFoundException::class);

        putenv("COMPRESSED_FILES_FOLDER=fake_folder");
        
        new SearchFiles();
    }

    /**
     * Test if application has SEARCH_FILES_FOLDER reading access rights
     *
     * @return void
     */
    public function test_reading_access_rights_to_search_folder()
    {
        $this->expectException(AccessRightsException::class);

        putenv("SEARCH_FILES_FOLDER=tests/compress/unaccessable");

        new SearchFiles();
    }

    /**
     * Test if application has COMPRESSED_FILES_FOLDER writing access rights
     *
     * @return void
     */
    public function test_writing_access_rights_to_compressed_folder()
    {
        $this->expectException(AccessRightsException::class);

        putenv("COMPRESSED_FILES_FOLDER=tests/compress/unaccessable");

        new SearchFiles();
    }

    /**
     * Test if SEARCH_FILES_FOLDER directory exists
     *
     * @return void
     */
    public function test_setup_works()
    {
        $exception = null;
        
        try {
            new SearchFiles();
        } catch (Exception $exception) {}

        $this->assertNull($exception, 'An exception was thrown.');
    }
}
