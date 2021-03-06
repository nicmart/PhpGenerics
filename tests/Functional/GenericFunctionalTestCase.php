<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */
namespace NicMart\Generics;

use NicMart\Generics\Autoloader\GenericAutoloaderFactory;

/**
 * Class GenericFunctionalTestCase
 * @package NicMart\Generics\Code
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GenericFunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $cacheDir;
    protected static $genericsCacheDir;

    public static function setUpBeforeClass()
    {
        self::$cacheDir = __DIR__ . "/../../cache";
        self::$genericsCacheDir = self::$cacheDir;

        GenericAutoloaderFactory::registerAutoloader(
            self::$genericsCacheDir
        );
    }


    public function tearDown()
    {
        $dir = __DIR__ . "/../../../cache";
        //exec("rm -rf $dir");
    }
}

abstract class Test {
    public function name()
    {
        return get_class($this);
    }
}

class A extends Test {}
class B extends Test {}
class C extends Test {}
class D extends Test {}
class E extends Test {}