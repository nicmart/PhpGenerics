<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Composer;


use Composer\Autoload\ClassLoader;

/**
 * Class NamespaceDirectoryResolver
 * @package NicMart\Generics\Composer
 */
class ClassLoaderDirectoryResolver implements DirectoryResolver
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * ClassLoaderDirectoryResolver constructor.
     * @param ClassLoader $classLoader
     */
    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    /**
     * @param $name
     * @return array
     */
    public function directories($name)
    {
        $directories = array();

        $logicalPath = strtr($name, '\\', DIRECTORY_SEPARATOR);
        $psr4Prefixes = $this->classLoader->getPrefixesPsr4();
        $fallbackDirsPsr4 = $this->classLoader->getFallbackDirsPsr4();
        $psr0Prefixes = $this->classLoader->getPrefixes();
        $fallbackDirsPsr0 = $this->classLoader->getFallbackDirs();

        // Psr4
        foreach ($psr4Prefixes as $prefix => $dirs) {
            if (0 !== strpos($name, $prefix)) {
                continue;
            }
            $suffix = substr($logicalPath, strlen($prefix));
            foreach ($dirs as $dir) {
                $directories[] = $dir . $suffix;
            }
        }

        foreach ($fallbackDirsPsr4 as $dir) {
            $directories[] = $dir . $logicalPath;
        }

        // Psr0
        foreach ($psr0Prefixes as $prefix => $dirs) {
            if (0 !== strpos($name, $prefix)) {
                continue;
            }
            foreach ($dirs as $dir) {
                $directories[] = $dir . $logicalPath;
            }
        }

        foreach ($fallbackDirsPsr0 as $dir) {
            $directories[] = $dir . $logicalPath;
        }


        return $directories;
    }
}