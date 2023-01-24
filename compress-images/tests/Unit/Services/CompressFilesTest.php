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
}
