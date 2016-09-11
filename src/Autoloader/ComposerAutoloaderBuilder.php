<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Autoloader;

use RuntimeException;

/**
 * Class ComposerAutoloaderBuilder
 * @package NicMart\Generics\Autoloader
 */
class ComposerAutoloaderBuilder
{
    public static function autoloader()
    {
        $baseDir = dirname(dirname(__DIR__));

        if (self::isComposerDependency($baseDir)) {
            return include dirname(dirname($baseDir)) . DIRECTORY_SEPARATOR . "autoload.php";
        }

        $path = $baseDir . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

        if (file_exists($path)) {
            return include $path;
        }

        throw new RuntimeException("Unable to find composer autoload.php file");
    }

    /**
     * @param $baseDir
     * @return bool
     */
    private static function isComposerDependency($baseDir)
    {
        $nicmartVendorFolder = dirname($baseDir);
        $vendorFolder = dirname($nicmartVendorFolder);

        return
            basename($baseDir) == "php-generics"
            && basename($nicmartVendorFolder) == "nicmart"
            && file_exists($vendorFolder . DIRECTORY_SEPARATOR . "autoload.php")
        ;
    }
}