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

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Path;
use NicMart\Generics\Name\RelativeName;

/**
 * Class Namespace_
 * @package NicMart\Generics\Name\Namespace_
 */
final class Namespace_
{
    /**
     * @var FullName
     */
    private $name;

    /**
     * @param string $string
     * @return Namespace_
     */
    public static function fromString($string)
    {
        return new self(FullName::fromString($string));
    }

    /**
     * @param string[] $parts
     * @return Namespace_
     */
    public static function fromParts(array $parts)
    {
        return new self(new FullName(new Path($parts)));
    }

    /**
     * @return Namespace_
     */
    public static function globalNamespace()
    {
        static $global;

        if (!$global) {
            $global = new self(new FullName(new Path));
        }

        return $global;
    }

    /**
     * Namespace_ constructor.
     * @param FullName $name
     */
    public function __construct(FullName $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->name->toString();
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param RelativeName $name
     * @return FullName
     */
    public function qualifyName(RelativeName $name)
    {
        if ($name->isNative()) {
            return new FullName($name->path());
        }

        return new FullName(
            $this->name->path()->append($name->path())
        );
    }

    /**
     * @param FullName $name
     * @return RelativeName
     */
    public function simplifyName(FullName $name)
    {
        $nsPath = $this->name->path();
        $namePath = $name->path();

        if (!$nsPath->isPrefixOf($namePath)) {
            return new RelativeName($namePath);
        }

        return new RelativeName(
            $namePath->from($nsPath)
        );
    }

    /**
     * @param Namespace_ $namespace
     * @return Namespace_
     */
    public function ancestor(Namespace_ $namespace)
    {
        return new self(new FullName(
            $this->name->path()->ancestor($namespace->name->path())
        ));
    }
}