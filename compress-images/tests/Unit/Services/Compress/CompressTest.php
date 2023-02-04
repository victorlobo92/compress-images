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
}
