<?php

namespace Tests\Unit\Services\Compress;

use App\Services\Compress\Compress;
use Error;
use PHPUnit\Framework\TestCase;

class CompressTest extends TestCase
{
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
}
