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
use NicMart\Generics\Type\Transformer\TypeTransformer;

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
     * @param FullName $name
     * @return bool
     */
    public static function isPrimitive(FullName $name)
    {
        switch ($name->toString()) {
            // PHP 5
            case "callable":
            case "static":
            case "array":
            case "self":

            // PHP 7
            case "string":
            case "int":
            case "resource":
            case "float":
            case "double":
            case "bool":
            case "void":

            // Unsupported
            case "mixed":
                return true;
        }

        return false;
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
     * @param TypeTransformer $typeTransformer
     * @return Type
     */
    public function map(TypeTransformer $typeTransformer)
    {
        return $this;
    }

    /**
     * @param callable $z
     * @param callable $fold
     * @return mixed
     */
    public function bottomUpFold($z, callable $fold)
    {
        return $fold($z, $this);
    }

    public function subTypes()
    {
        return [];
    }


    /**
     * @return bool
     */
    public function isSupportedType()
    {
        static $isPhp7;

        if (!isset($isPhp7)) {
            $isPhp7 = version_compare(phpversion(), '7.0.0', '>=');
        }

        $name = $this->name->toString();

        if ($isPhp7) {
            return $name != "mixed";
        }

        switch ($this->name->toString()) {
            case "callable":
            case "static":
            case "array":
            case "self":
                return true;
        }

        return false;
    }

    /**
     * @param FullName $name
     */
    private function assertValidName(FullName $name)
    {
        if (!$this->isPrimitive($name)) {
            throw new InvalidArgumentException(
                "Invalid primitive type name"
            );
        }
    }
}