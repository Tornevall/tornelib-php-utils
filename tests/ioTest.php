<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use TorneLIB\Utils\Memory;

class ioTest extends TestCase
{
    /**
     * @test
     * Adjust memory on fly.
     */
    public function testMemoryLimit()
    {
        $current = ini_get('memory_limit');
        (new Memory())->setMemoryLimit('2048M');
        $newCurrent = ini_get('memory_limit');

        static::assertTrue($current !== $newCurrent && $newCurrent === '2048M');
    }

    /**
     * @test
     * Adjust memory on fly.
     * @throws Exception
     */
    public function getMemoryLimitAdjusted()
    {
        $mem = new Memory();
        $mem->setMemoryLimit('2048M');
        static::assertTrue(
            (new Memory())->getMemoryLimitAdjusted('4096M')
        );
    }
}
