<?php

namespace Tests\Unit\Services;

use App\Exceptions\AccessRightsException;
use App\Exceptions\FileOrFolderNotFoundException;
use App\Exceptions\MissingEnvironmentVariableException;
use App\Services\Compress\CompressInterface;
use App\Services\Compress\CompressPNG;
use App\Services\CompressFiles;
use Exception;
use Tests\TestCase;

class CompressFilesTest extends TestCase
{
    private $compress_mock;

    private function get_base_path()
    {
        $base_path = trim(getenv('COMPRESS_FOLDER_PATH'), '/');

        if (!$base_path) $base_path = base_path();

        return $base_path;
    }

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
        putenv("MAX_FILE_SIZE_MB=" . env('MAX_FILE_SIZE_MB'));
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

    /**
     * Test compress method is called for PNG file
     *
     * @return void
     */
    public function test_compress_method_is_called_for_png_file()
    {
        $compress_png_mock = $this->createMock(CompressPNG::class);

        $compress_png_mock->method('setup')->willReturn($compress_png_mock);

        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        $compressed_file_folder = $this->get_base_path() . '/' . trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';
        
        $file_to_compress = [
            [
                'folder' => "$search_file_folder/accessable/",
                'name' => 'dog.jpg',
            ],
            [
                'folder' => "$search_file_folder/accessable/",
                'name' => 'dog.png',
            ],
            [
                'folder' => "$search_file_folder/accessable/",
                'name' => 'dog_2.png',
            ],
        ];

        $first_file = array_merge($file_to_compress[1], [
            'path' => $file_to_compress[1]['folder'] . $file_to_compress[1]['name'],
            'mime' => 'image/png',
            'size' => filesize($file_to_compress[1]['folder'] . $file_to_compress[1]['name'],)
        ]);

        $second_file = array_merge($file_to_compress[2], [
            'path' => $file_to_compress[2]['folder'] . $file_to_compress[2]['name'],
            'mime' => 'image/png',
            'size' => filesize($file_to_compress[2]['folder'] . $file_to_compress[2]['name'])
        ]);

        $compress_png_mock->expects($this->exactly(2))
            ->method('setup')
            ->withConsecutive(
                [$first_file, $compressed_file_folder],
                [$second_file, $compressed_file_folder]
            );

        $compress_png_mock->expects($this->exactly(2))->method('compress');

        $compress_files = new CompressFiles($compress_png_mock, $file_to_compress);
        $compress_files->compress();
    }

    /**
     * Test exception is thrown when file to compress is missing
     *
     * @return void
     */
    public function test_exception_thrown_if_file_is_missing()
    {
        $compress_png_mock = $this->createMock(CompressPNG::class);

        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            [
                'folder' => "$search_file_folder/accessable/",
                'name' => 'fake_file',
            ]
        ];

        $file_path = $file_to_compress[0]['folder'] . $file_to_compress[0]['name'];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The image on path '$file_path' is missing!");

        $compress_files = new CompressFiles($compress_png_mock, $file_to_compress);
        $compress_files->compress();
    }

    /**
     * Test exception is thrown when file format is not supported by the compressor
     *
     * @return void
     */
    public function test_exception_thrown_if_file_format_is_not_supported()
    {
        $compress_png_mock = $this->createMock(CompressPNG::class);

        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            [
                'folder' => "$search_file_folder/accessable/",
                'name' => 'unsupported_format.txt',
            ]
        ];

        $file_path = $file_to_compress[0]['folder'] . $file_to_compress[0]['name'];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The image '$file_path' is not supported!");

        $compress_files = new CompressFiles($compress_png_mock, $file_to_compress);
        $compress_files->compress();
    }

    /**
     * Test exception is thrown when file is bigger then max file size
     *
     * @return void
     */
    public function test_exception_thrown_if_file_is_too_big()
    {
        $compress_png_mock = $this->createMock(CompressPNG::class);

        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            [
                'folder' => "$search_file_folder/accessable/",
                'name' => 'dog.png',
            ]
        ];

        putenv('MAX_FILE_SIZE_MB=0.1');
        $max_file_size_mb = getenv('MAX_FILE_SIZE_MB');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Please send a imagem smaller than {$max_file_size_mb}mb!");

        $compress_files = new CompressFiles($compress_png_mock, $file_to_compress);
        $compress_files->compress();
    }
}
