<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor\Type;


use NicMart\Generics\Type\Transformer\TypeTransformer;
use NicMart\Generics\Type\Type;
use phpDocumentor\Reflection\Type as PhpDocType;

/**
 * Class AnnotatedType
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
class AnnotatedType implements PhpDocType
{
    /**
     * @var PhpDocType
     */
    private $phpDocType;

    /**
     * @var Type
     */
    private $type;

    /**
     * AnnotatedType constructor.
     * @param PhpDocType $phpDocType
     * @param Type $type
     */
    public function __construct(PhpDocType $phpDocType, Type $type)
    {
        $this->phpDocType = $phpDocType;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->phpDocType;
    }

    /**
     * @return Type
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @return PhpDocType
     */
    public function phpDocType()
    {
        return $this->phpDocType;
    }

    /**
     * @param Type $type
     * @return AnnotatedType
     */
    public function withType(Type $type)
    {
        $new = clone $this;

        $new->type = $type;

        return $new;
    }

    /**
     * @param TypeTransformer $typeTransformer
     * @return AnnotatedType
     */
    public function transformType(TypeTransformer $typeTransformer)
    {
        return $this->withType($typeTransformer->transform($this->type));
    }
}