<?php

namespace Tests\Unit\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
use App\Services\Compress\CompressInterface;
use App\Services\CompressFiles;
use Tests\TestCase;

class CompressFilesTest extends TestCase
{
    private $compress_mock;

    /**
     * @before
     */
    public function setup_shared_dependency(): void
    {
        $this->compress_mock = $this->createMock(CompressInterface::class);
    }

    /**
     * @after
     */
    public function tear_down_environment_changes()
    {
        putenv("COMPRESSED_FILES_FOLDER=" . env('COMPRESSED_FILES_FOLDER'));
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
        
        new CompressFiles($this->compress_mock, []);
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
        
        new CompressFiles($this->compress_mock, []);
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

        new CompressFiles($this->compress_mock, []);
    }

    /**
     * Test if CompressFiles has attributes
     *
     * @return void
     */
    public function test_class_has_attributes()
    {
        $this->assertClassHasAttribute('files_to_compress', CompressFiles::class);
        $this->assertClassHasAttribute('files_compressed', CompressFiles::class);
    }
}
