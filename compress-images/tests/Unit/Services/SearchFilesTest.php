<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\SearchFiles;

class SearchFilesTest extends TestCase
{
    /**
     * @after
     */
    public function tearDownEnvironmentChanges()
    {
        putenv("SEARCH_FILE_FOLDER=" . env('SEARCH_FILE_FOLDER'));
    }

    /**
     * Test if missing environment variable SEARCH_FILE_FOLDER throws exception
     *
     * @return void
     */
    public function test_missing_search_file_folder_env()
    {
        $this->expectException(\Exception::class);

        putenv("SEARCH_FILE_FOLDER=");

        new SearchFiles();
    }
}
