<?php

namespace Tests\Unit\Services\Compress;

use App\Services\Compress\Compress;
use Error;
use Exception;
use PHPUnit\Framework\TestCase;

class CompressTest extends TestCase
{
    private function get_base_path()
    {
        $base_path = trim(getenv('COMPRESS_FOLDER_PATH'), '/');

        if (!$base_path) $base_path = base_path();

        return $base_path;
    }

    public static function delTree($dir) {

        $files = array_diff(scandir($dir), array('.','..'));
     
        foreach ($files as $file) {
     
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
     
        }
     
        return rmdir($dir);
     
    }

    /**
     * @after
     */
    public function tear_down_environment_changes()
    {
        putenv("COMPRESSED_FILES_FOLDER=" . env('COMPRESSED_FILES_FOLDER'));

        if (!empty($_SESSION['del_tree'])) {
            self::delTree($_SESSION['del_tree']);
            unset($_SESSION['del_tree']);
        }
    }

    /**
     * Throw exception when trying to instanciate abstract class directly
     *
     * @return void
     */
    public function test_exception_when_trying_to_instanciate_directly()
    {
        $this->expectException(Error::class);

        new Compress();
    }

    /**
     * Throw exception when property quality is empty
     *
     * @return void
     */
    public function test_exception_when_property_quality_is_empty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Please inform the quality!');

        $compressMock = $this->createPartialMock(Compress::class, []);

        $compressMock->setup([], '');
    }

    /**
     * Test if method setup calls validate method
     *
     * @return void
     */
    public function test_if_setup_calls_validate_method()
    {
        $compressMock = $this->createPartialMock(Compress::class, ['validate']);
        $compressMock->expects($this->once())->method('validate');
        $compressMock->setup([], '');
    }

    /**
     * Test mkdir is working propertly
     *
     * @return void
     */
    public function test_mkdir_is_working_propertly()
    {
        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            'folder' => "$search_file_folder/accessable/",
            'name' => 'dog_2.png'
        ];

        $compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';

        $test_mkdir_folder = $compressed_files_folder . 'test_mkdir';

        putenv("COMPRESSED_FILES_FOLDER=$test_mkdir_folder");

        $compressed_files_folder = $this->get_base_path() . '/' . $compressed_files_folder;

        $compressMock = $this->createPartialMock(Compress::class, ['validate']);
        $compressMock->setup($file_to_compress, $test_mkdir_folder . '/');
        $compressMock->compress();
        
        $_SESSION['del_tree'] = $test_mkdir_folder;

        $this->assertTrue(is_dir($compressed_files_folder));
    }

    /**
     * compress_file method has been called
     *
     * @return void
     */
    public function test_compress_file_method_has_been_called()
    {
        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            'folder' => "$search_file_folder/accessable/",
            'name' => 'dog_2.png',
        ];

        $file_to_compress['path'] = $file_to_compress['folder'] . $file_to_compress['name'];

        $compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';
        
        $compressMock = $this->createPartialMock(Compress::class, ['validate', 'make_dir_if_needed', 'compress_file']);
        $compressMock->expects($this->once())->method('compress_file');

        $compressMock->setup($file_to_compress, $compressed_files_folder);
        $compressMock->compress();
    }

    /**
     * compress_file method must be overwritten
     *
     * @return void
     */
    public function test_compress_file_method_must_be_overwritten()
    {
        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            'folder' => "$search_file_folder/accessable/",
            'name' => 'dog_2.png',
        ];

        $file_to_compress['path'] = $file_to_compress['folder'] . $file_to_compress['name'];

        $compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';
        
        $compressMock = $this->createPartialMock(Compress::class, ['validate', 'make_dir_if_needed']);
        $compressMock->setup($file_to_compress, $compressed_files_folder);
        
        $expected_message = "Method 'compress_file' not implemented for class '" . get_class($compressMock) . "'!";
        $this->assertEquals($expected_message, $compressMock->compress());
    }

    /**
     * compression_worked method has been called
     *
     * @return void
     */
    public function test_compression_worked_method_has_been_called()
    {
        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            'folder' => "$search_file_folder/accessable/",
            'name' => 'dog_2.png',
        ];

        $file_to_compress['path'] = $file_to_compress['folder'] . $file_to_compress['name'];

        $compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';
        
        $compressMock = $this->createPartialMock(Compress::class, ['validate', 'make_dir_if_needed', 'compress_file', 'compression_worked']);
        $compressMock->expects($this->once())->method('compression_worked');
        
        $compressMock->setup($file_to_compress, $compressed_files_folder);

        $expected_message = 'File compression resulted in a bigger file';
        $this->assertEquals($expected_message, $compressMock->compress());
    }

    /**
     * Temporary file is moved to compressed folder on success
     *
     * @return void
     */
    public function test_temp_file_is_moved_to_compressed_folder()
    {
        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');
        
        $file_to_compress = [
            'folder' => "$search_file_folder/accessable/",
            'name' => 'dog_2.png',
        ];

        $file_to_compress['path'] = $file_to_compress['folder'] . $file_to_compress['name'];
        
        $compressed_files_folder = trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';

        $file_destination_path = $compressed_files_folder . 'accessable/dog_2.png';

        $compressMock = $this->createPartialMock(Compress::class, ['validate', 'make_dir_if_needed', 'compress_file', 'compression_worked']);
        $compressMock->method('compression_worked')
            ->willReturn(true);

        $compressMock->setup($file_to_compress, $compressed_files_folder);

        $this->assertEquals($file_destination_path, $compressMock->compress());
    }
}
