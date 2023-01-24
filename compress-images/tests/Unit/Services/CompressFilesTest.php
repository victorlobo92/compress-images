<?php

namespace Tests\Unit\Services;

use App\Services\CompressFilesInterface;
use App\Services\CompressFiles;
use Tests\TestCase;

class CompressFilesTest extends TestCase
{
    /**
     * Test if CompressFiles class implements CompressFilesInterface
     *
     * @return void
     */
    public function test_class_implements_interface()
    {
        $compressFiles = new CompressFiles([]);

        $this->assertInstanceOf(CompressFilesInterface::class, $compressFiles);
    }

    /**
     * Test if CompressFiles has attribute files_to_compress
     *
     * @return void
     */
    public function test_class_has_attributes()
    {
        $this->assertClassHasAttribute('files_to_compress', CompressFiles::class);
        $this->assertClassHasAttribute('files_compressed', CompressFiles::class);
    }
}
