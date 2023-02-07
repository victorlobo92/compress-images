<?php

namespace Tests\Unit\Services;

use App\Services\Compress\CompressInterface;
use App\Services\Compress\CompressPNG;
use Tests\TestCase;

class CompressPNGTest extends TestCase
{
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
}
