<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use TorneLIB\Exception\ExceptionHandler;
use TorneLIB\Utils\Memory;
use TorneLIB\Utils\Generic;
use TorneLIB\Utils\Security;

class utilsTest extends TestCase
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

    /**
     * @test
     * @throws ReflectionException
     */
    public function getDocBlockVersion()
    {
        static::assertTrue(
            version_compare(
                (new Generic())->getVersionByClassDoc(),
                '6.1.0',
                '>='
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getDocBlockThrows()
    {
        static::assertTrue(
            (new Generic())->getDocBlockItem(
                'throws',
                'getDocBlockItem'
            ) === 'ReflectionException'
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getDocBlockSince()
    {
        $sinceString = (new Generic())->getDocBlockItem(
            '@since',
            'getDocBlockItem'
        );

        static::assertTrue(
            version_compare($sinceString, '6.1.0', '>=') ? true : false
        );
    }

    /**
     * @test
     * @throws ExceptionHandler
     */
    public function methodStates() {
        static::expectException(ExceptionHandler::class);
        (new Security())->getFunctionState('nisse');
    }
}
