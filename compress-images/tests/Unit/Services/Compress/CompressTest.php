<?php

namespace Tests\Unit\Services\Compress;

use App\Services\Compress\Compress;
use Error;
use Exception;
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
}
