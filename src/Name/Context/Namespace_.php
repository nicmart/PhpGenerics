<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Context;

use NicMart\Generics\Name\Path;

/**
 * Class Namespace_
 * @package NicMart\Generics\Name\Namespace_
 */
final class Namespace_
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @param string $string
     * @return Namespace_
     */
    public static function fromString($string)
    {
        return new self(Path::fromString($string));
    }

    /**
     * @param string[] $parts
     * @return Namespace_
     */
    public static function fromParts(array $parts)
    {
        return new self(new Path($parts));
    }

    /**
     * @return Namespace_
     */
    public static function globalNamespace()
    {
        static $global;
        if (!$global) {
            $global = new self(new Path());
        }

        return $global;
    }

    /**
     * Namespace_ constructor.
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->path->toString("\\");
    }

    /**
     * @return Path
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @param Namespace_ $namespace
     * @return Namespace_
     */
    public function commonAncestor(Namespace_ $namespace)
    {
        return new self(
            $this->path()->ancestor($namespace->path())
        );
    }
}