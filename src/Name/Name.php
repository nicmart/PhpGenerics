<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 05/04/2016, 10:45
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name;

use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Class Path
 * @package NicMart\Generics\Name
 */
abstract class Name
{
    /**
     * @var string[]
     */
    private $parts = array();

    /**
     * Returns the root path object
     *
     * @return static
     */
    public static function root()
    {
        static $root = null;

        if (!$root) {
            $root = new static(array());
        }

        return $root;
    }

    /**
     * @param string $name
     * @param string $separator
     *
     * @return static
     */
    public static function fromString($name, $separator = "\\")
    {
        $name = ltrim((string) $name, $separator);

        if (!$name) {
            return self::root();
        }

        return new static(explode($separator, $name));
    }

    /**
     * Path constructor.
     * @param string[] $parts
     */
    final public function __construct(array $parts = array())
    {
        $this->assertValidClass();

        foreach ($parts as $part) {
            $this->parts[] = (string) $part;
        }
    }

    /**
     * @return bool
     */
    abstract function isFullName();

    /**
     * @return string[]
     */
    public function parts()
    {
        return $this->parts;
    }

    /**
     * @return int
     */
    public function length()
    {
        return count($this->parts);
    }

    /**
     * @param Name|static $name
     * @return bool
     */
    public function isPrefixOf(Name $name)
    {
        if ($this->length() > $name->length()) {
            return false;
        }

        foreach ($this->parts as $i => $part) {
            if ($name->parts[$i] != $part) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return SimpleName
     */
    public function last()
    {
        if ($this->isRoot()) {
            // @todo create domain exception
            throw new \UnderflowException("Root path does not have names");
        }

        return new SimpleName($this->parts[$this->length() - 1]);
    }

    /**
     * @return SimpleName
     */
    public function first()
    {
        if ($this->isRoot()) {
            // @todo create domain exception
            throw new \UnderflowException("Root path does not have first part");
        }

        return new SimpleName($this->parts[0]);
    }

    /**
     * @return static
     */
    public function tail()
    {
        return new static(array_slice($this->parts, 1));
    }

    /**
     * @return static
     */
    public function up()
    {
        return new static(array_slice($this->parts, 0, -1));
    }

    /**
     * @param string $name
     * @return static
     */
    public function down($name)
    {
        $parts = $this->parts;
        $parts[] = (string) $name;

        return new static($parts);
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return !(bool) $this->parts();
    }

    /**
     * @param Name $name
     * @return static
     */
    public function ancestor(Name $name)
    {
        $parts1 = $this->parts();
        $parts2 = $name->parts();
        $min = min(count($parts1), count($parts2));
        $commonAncestorParts = array();

        for ($i = 0; $i < $min; $i++) {
            if ($parts1[$i] != $parts2[$i]) {
                break;
            }
            $commonAncestorParts[] = $parts1[$i];
        }

        return new static($commonAncestorParts);
    }

    /**
     * @param Name $name
     * @return static
     */
    public function from(Name $name)
    {
        $ancestor = $name->ancestor($this);

        if ($name->length() != $ancestor->length()) {
            return $this;
        }

        return new static(array_slice(
            $this->parts,
            $ancestor->length()
        ));
    }

    /**
     * @param Name $name
     * @return Name
     */
    public function append(Name $name)
    {
        return new static(array_merge(
            $this->parts(),
            $name->parts()
        ));
    }

    /**
     * @param string $separator
     * @return string
     */
    public function toString($separator = "\\")
    {
        return implode($separator, $this->parts());
    }

    /**
     * @return FullName
     */
    public function toFullName()
    {
        return new FullName($this->parts());
    }

    /**
     * @return bool
     */
    private function assertValidClass()
    {
        if (
               $this instanceof FullName
            || $this instanceof RelativeName
        ) {
            return true;
        }

        throw new RuntimeException("Invalid name class");
    }
}