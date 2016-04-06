<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 05/04/2016, 15:14
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name;

/**
 * Class SimpleName
 * @package NicMart\Generics\Name
 */
final class SimpleName
{
    /**
     * @var string
     */
    private $name;

    /**
     * SimpleName constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }

    /**
     * @return RelativeName
     */
    public function toRelativeType()
    {
        return new RelativeName(Path::fromString($this->name));
    }

    /**
     * @return Path
     */
    public function toPath()
    {
        return new Path(array($this->name));
    }
}