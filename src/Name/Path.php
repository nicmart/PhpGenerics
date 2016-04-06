<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 05/04/2016, 10:45
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name;

use Doctrine\Instantiator\Exception\InvalidArgumentException;

/**
 * Class Path
 * @package NicMart\Generics\Name
 */
final class Path
{
    /**
     * @var string[]
     */
    private $parts = array();

    /**
     * Returns the root path object
     *
     * @return Path
     */
    public static function root()
    {
        static $root = null;

        if (!$root) {
            $root = new self(array());
        }

        return $root;
    }

    /**
     * @param string $path
     * @param string $separator
     *
     * @return Path
     */
    public static function fromString($path, $separator = "\\")
    {
        $path = ltrim((string) $path, $separator);

        if (!$path) {
            return self::root();
        }

        return new self(explode($separator, $path));
    }

    /**
     * Path constructor.
     * @param string[] $parts
     */
    public function __construct(array $parts = array())
    {
        foreach ($parts as &$part) {
            $this->parts[] = (string) $part;
        }
    }

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
     * @param Path $path
     * @return bool
     */
    public function isPrefixOf(self $path)
    {
        if ($this->length() > $path->length()) {
            return false;
        }

        foreach ($this->parts as $i => $part) {
            if ($path->parts[$i] != $part) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function name()
    {
        if ($this->isRoot()) {
            // @todo create domain exception
            throw new \UnderflowException("Root path does not have names");
        }

        return $this->parts[$this->length() - 1];
    }

    /**
     * @return string
     */
    public function first()
    {
        if ($this->isRoot()) {
            // @todo create domain exception
            throw new \UnderflowException("Root path does not have first part");
        }

        return $this->parts[0];
    }

    /**
     * @return Path
     */
    public function tail()
    {
        return new self(array_slice($this->parts, 1));
    }

    /**
     * @return Path
     */
    public function up()
    {
        return new self(array_slice($this->parts, 0, -1));
    }

    /**
     * @param string $name
     * @return Path
     */
    public function down($name)
    {
        $parts = $this->parts;
        $parts[] = (string) $name;

        return new self($parts);
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return !(bool) $this->parts();
    }

    /**
     * @param Path $path
     * @return Path
     */
    public function ancestor(self $path)
    {
        $parts1 = $this->parts();
        $parts2 = $path->parts();
        $min = min(count($parts1), count($parts2));
        $commonAncestorParts = array();

        for ($i = 0; $i < $min; $i++) {
            if ($parts1[$i] != $parts2[$i]) {
                break;
            }
            $commonAncestorParts[] = $parts1[$i];
        }

        return new self($commonAncestorParts);
    }

    /**
     * @param Path $path
     * @return Path
     */
    public function from(Path $path)
    {
        $ancestor = $path->ancestor($this);

        if ($path->length() != $ancestor->length()) {
            return $this;
        }

        return new self(array_slice(
            $this->parts,
            $ancestor->length()
        ));
    }

    /**
     * @param Path $path
     * @return Path
     */
    public function append(Path $path)
    {
        return new self(array_merge(
            $this->parts(),
            $path->parts()
        ));
    }

    /**
     * @param Path $path
     * @return Path
     */
    public function prepend(Path $path)
    {
        return $path->append($this);
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
     * @param string $separator
     * @return string
     */
    public function toAbsoluteString($separator = "\\")
    {
        return $separator . $this->toString($separator);
    }
}