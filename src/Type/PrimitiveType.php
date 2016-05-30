<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;


use InvalidArgumentException;
use NicMart\Generics\Name\FullName;

/**
 * Class PrimitiveType
 * @package NicMart\Generics\Type
 */
final class PrimitiveType implements Type
{
    /**
     * @var FullName
     */
    private $name;

    /**
     * @param $string
     * @return PrimitiveType
     */
    public static function fromString($string)
    {
        return new self(FullName::fromString($string));
    }

    /**
     * PrimitiveType constructor.
     * @param FullName $name
     */
    public function __construct(FullName $name)
    {
        $this->assertValidName($name);
        
        $this->name = $name;
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }


    /**
     * @param FullName $name
     */
    private function assertValidName(FullName $name)
    {
        if (!count($name->parts()) != 1) {
            throw new InvalidArgumentException(
                "Invalid primitive type name, too many parts"
            );
        }

        switch ($name->toString()) {
            case "string":
            case "int":
            case "callable":
            case "array":
            case "resource":
            case "float":
            case "double":
            case "bool":
            case "void":
            case "static":
            case "self":
            case "parent":
                return;
        }

        throw new InvalidArgumentException(
            "Invalid primitive type name"
        );
    }
}