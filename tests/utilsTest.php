<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use TorneLIB\Exception\Constants;
use TorneLIB\Exception\ExceptionHandler;
use TorneLIB\Utils\Generic;
use TorneLIB\Utils\Memory;
use TorneLIB\Utils\Security;

/**
 * Class utilsTest
 * @version 1.0.0
 */
class utilsTest extends TestCase
{
    private $wpPath = '/usr/local/apache2/htdocs/ecommerceweb.se/woocommerce.ecommerceweb.se/wp-content/plugins/tornevalls-resurs-bank-payment-gateway-for-woocommerce';

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
     * Adjust memory statically.
     */
    public function testMemoryStatic()
    {
        $current = ini_get('memory_limit');
        Memory::setMemory('4096M');
        $newCurrent = ini_get('memory_limit');

        static::assertTrue($current !== $newCurrent && $newCurrent === '4096M');
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
     * Adjust memory on fly.
     * @throws Exception
     */
    public function getMemoryStaticAdjusted()
    {
        $mem = new Memory();
        $mem->setMemoryLimit('4096M');
        static::assertTrue(
            Memory::getMemoryAdjusted('8192M')
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
    public function methodStates()
    {
        static::expectException(ExceptionHandler::class);
        (new Security())->getFunctionState('nisse');
    }

    /**
     * @test
     * @throws ExceptionHandler
     */
    public function getVersionByComposer()
    {
        static::assertStringStartsWith(
            '6.1',
            (new Generic())->getVersionByComposer(__FILE__)
        );
    }

    /**
     * @test
     */
    public function getTemplate()
    {
        $code = 0;
        $generic = new Generic();
        $generic->setTemplatePath(__DIR__ . '/templates');
        $html = $generic->getTemplate('test.html', ['$username' => 'Sven', 'regularVariable' => 'Yep, it is regular.']);

        try {
            $uglyRequest = new Generic();
            $uglyRequest->getTemplate(
                '/etc/passwd',
                [
                    '$username' => 'Sven',
                    'regularVariable' => 'Yep, it is regular.',
                ]
            );
        } catch (Exception $e) {
            $code = $e->getCode();
        }

        static::assertTrue(
            $code === 404 &&
            (bool)preg_match('/it is regular/', $html)
        );
    }

    /**
     * @test
     * @since 6.1.9
     */
    public function getClassShort()
    {
        $withReflection = (new Generic())->getShortClassName(Generic::class);
        $withoutReflection = (new Generic())->getShortClassName(Generic::class, true);

        static::assertTrue(
            $withoutReflection === 'Generic' &&
            $withReflection === 'Generic'
        );
    }

    /**
     * @test
     * @since 6.1.12
     */
    public function getComposerVersion()
    {
        $composerVersion = (new Generic())->getVersionByComposer(__FILE__);

        static::assertTrue(version_compare($composerVersion, '6.1.12', '>='));
    }

    /**
     * @test
     * @since 6.1.12
     */
    public function getComposerName()
    {
        $composerName = (new Generic())->getComposerTag(__FILE__, 'name');

        static::assertTrue($composerName === 'tornevall/tornelib-php-utils');
    }

    /**
     * @test
     * @throws ExceptionHandler
     */
    public function getComposerNameShort()
    {
        $composerName = (new Generic())->getComposerShortName(__FILE__);

        static::assertTrue($composerName === 'tornelib-php-utils');
    }

    /**
     * @test
     */
    public function getComposerVendor()
    {
        $composerName = (new Generic())->getComposerVendor(__FILE__);

        static::assertTrue($composerName === 'tornevall');
    }

    /**
     * @test
     * @since 6.1.17
     */
    public function getComposerInSecureMode()
    {
        if (!(new Security())->getSecureMode()) {
            static::markTestSkipped('PHP instance is not in secure mode (open_basedir), so this test will not run.');
            return;
        }

        $generic = new Generic();

        static::assertTrue($generic->getVersionByComposer($this->wpPath) === 'N/A (open_basedir security active)');
    }

    /**
     * @test
     * @since 6.1.18
     */
    public function getExpects()
    {
        $gen = new Generic();
        $gen->setExpectedVersions(
            [
                Generic::class => '6.1.18',
            ]
        );

        $expectsTrue = $gen->getExpectedVersions();

        $gen->setExpectedVersions(
            [
                Constants::class => '9999.99',
            ]
        );

        $expectsFalse = $gen->getExpectedVersions();

        $gen->setExpectedVersions(
            [
                Constants::class => '9999.99',
            ]
        );
        $oneException = false;
        try {
            $gen->getExpectedVersions(false);
        } catch (Exception $e) {
            $oneException = true;
        }

        $castWrongClass = false;
        try {
            $gen->setExpectedVersions(
                ['No-Class-Found' => '9.99']
            );
            $gen->getExpectedVersions();
        } catch (Exception $e) {
            $castWrongClass = true;
        }

        $ncTest = realpath(__DIR__ . '/../../tornelib-php-netcurl');
        if ($ncTest) {
            $gen->setExpectedVersions(
                [
                    $ncTest => '6.1.0',
                    sprintf('%s/composer.json', $ncTest) => '6.1.0',
                ]
            );
            $expectAlsoNetCurl = $gen->getExpectedVersions();

            static::assertTrue(
                $expectsTrue &&
                !$expectsFalse &&
                $expectAlsoNetCurl &&
                $oneException &&
                $castWrongClass
            );
            return;
        }

        static::assertTrue(
            $expectsTrue &&
            !$expectsFalse &&
            $oneException &&
            $castWrongClass
        );
    }

    /**
     * @test
     * @since 6.1.19
     */
    public function realExpects()
    {
        $generic = new Generic();
        $ncTest = realpath(__DIR__ . '/../../tornelib-php-netcurl');

        $expect = [
            Constants::class => '9999.99',
        ];
        if ($ncTest) {
            $expect[$ncTest] = '9999.99';
        }
        $generic->setExpectedVersions($expect);

        $result = $generic->getExpectationsReal();
        $constants = $result['TorneLIB\Exception\Constants'];
        static::assertTrue(count($result)>=1 && (bool)preg_match('/^6.1/', $constants));
    }
}
