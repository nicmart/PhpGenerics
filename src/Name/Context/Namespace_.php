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
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Name\Transformer\NameSimplifier;

/**
 * Class Namespace_
 * @package NicMart\Generics\Name\Namespace_
 */
final class Namespace_ implements NameSimplifier, NameQualifier
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
        return new self(new FullName($parts));
    }

    /**
     * @return Namespace_
     */
    public static function globalNamespace()
    {
        static $global;

        if (!$global) {
            $global = new self(new FullName());
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
    public function qualify(RelativeName $name)
    {
        if ($name->isNative()) {
            return new FullName($name->parts());
        }

        return $this->name->append($name);
    }

    /**
     * @param FullName $name
     * @return RelativeName
     */
    public function simplify(FullName $name)
    {
        if (!$this->name->isPrefixOf($name)) {
            return new RelativeName($name->parts());
        }

        return $name->from($this->name)->toRelative();
    }

    /**
     * @param Namespace_ $namespace
     * @return Namespace_
     */
    public function ancestor(Namespace_ $namespace)
    {
        return new self($this->name->ancestor($namespace->name()));
    }
}