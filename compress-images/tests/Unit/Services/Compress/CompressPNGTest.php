<?php

namespace Tests\Unit\Services;

use App\Services\Compress\CompressInterface;
use App\Services\Compress\CompressPNG;
use Tests\TestCase;

class CompressPNGTest extends TestCase
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
        if (!empty($_SESSION['del_tree'])) {
            self::delTree($_SESSION['del_tree']);
            unset($_SESSION['del_tree']);
        }
    }

    /**
     * Test if Compress class implements CompressInterface
     *
     * @return void
     */
    public function test_class_implements_interface()
    {
        $compress = new CompressPNG([
            'folder' => 'fake_folder',
            'name' => 'fake_file.png'
        ], 'fake_destination_folder');

        $this->assertInstanceOf(CompressInterface::class, $compress);
    }

    /**
     * Don't throw exception when instanciated
     *
     * @return void
     */
    public function test_no_exception_when_instanciating()
    {
        $this->expectNotToPerformAssertions();

        new CompressPNG();
    }

    /**
     * Method compress_file has been called from child class
     *
     * @return void
     */
    public function test_method_compress_file_has_been_called_from_child_class()
    {
        $search_file_folder = $this->get_base_path() . '/' . trim(getenv('SEARCH_FILES_FOLDER'), '/');

        $file_to_compress = [
            'folder' => "$search_file_folder/accessable/",
            'name' => 'dog_2.png'
        ];

        $file_to_compress['path'] = $file_to_compress['folder'] . $file_to_compress['name'];

        $compressed_files_folder = $this->get_base_path() . '/' . trim(getenv('COMPRESSED_FILES_FOLDER'), '/') . '/';

        $file_destionation_path = $compressed_files_folder . 'accessable/dog_2.png';

        $compressPNG = new CompressPNG();
        $compressPNG->setup($file_to_compress, $compressed_files_folder);
        $this->assertEquals($file_destionation_path, $compressPNG->compress());

        $_SESSION['del_tree'] = $compressed_files_folder . 'accessable';
    }
}
