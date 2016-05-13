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

class NamespaceDirectoryResolver
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

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
        $prefixLengthsPsr4 = $this->classLoader->getPrefixesPsr4();
        $fallbackDirsPsr4 = $this->classLoader->getFallbackDirsPsr4();
        $fallbackDirsPsr4 = $this->classLoader->getFallbackDirsPsr4();

        // PSR-4 lookup
        $first = $name[0];
        if (isset($this->prefixLengthsPsr4[$first])) {
            foreach ($this->prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($name, $prefix)) {
                    foreach ($this->prefixDirsPsr4[$prefix] as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach ($this->fallbackDirsPsr4 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4)) {
                return $file;
            }
        }

        // PSR-0 lookup
        if (false !== $pos = strrpos($name, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
                . strtr(substr($logicalPathPsr4, $pos + 1), '_', DIRECTORY_SEPARATOR);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($name, '_', DIRECTORY_SEPARATOR) . ".php";
        }

        if (isset($this->prefixesPsr0[$first])) {
            foreach ($this->prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        foreach ($this->fallbackDirsPsr0 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                return $file;
            }
        }

        // PSR-0 include paths.
        if ($this->useIncludePath && $file = stream_resolve_include_path($logicalPathPsr0)) {
            return $file;
        }
    }
}