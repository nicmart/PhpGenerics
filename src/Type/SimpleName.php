<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 05/04/2016, 15:14
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Type;

/**
 * Class SimpleName
 * @package NicMart\Generics\Type
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
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return RelativeType
     */
    public function toRelativeType()
    {
        return new RelativeType($this->name);
    }

    /**
     * @return Path
     */
    public function toPath()
    {
        return new Path(array($this->name));
    }
}