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


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Transformer\TypeTransformer;

/**
 * Class SimpleReferenceType
 * @package NicMart\Generics\Type
 */
class SimpleReferenceType implements ReferenceType
{
    /**
     * @var FullName
     */
    private $fullName;

    /**
     * SimpleReferenceType constructor.
     * @param FullName $fullName
     */
    public function __construct(FullName $fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->fullName;
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
}