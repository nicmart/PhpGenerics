<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Compiler;

use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Type;

/**
 * Class CompilationResult
 * @package NicMart\Generics\Type\Compiler
 */
final class CompilationResult
{
    /**
     * @var SourceUnit
     */
    private $sourceUnit;

    /**
     * @var Type[]
     */
    private $transformedTypes = [];
    /**
     * @var TypeSerializer
     */
    private $serializer;

    /**
     * CompilationResult constructor.
     * @param SourceUnit $sourceUnit
     * @param TypeSerializer $serializer
     * @param array $transformedTypes
     */
    public function __construct(
        SourceUnit $sourceUnit,
        TypeSerializer $serializer,
        array $transformedTypes
    ) {
        $this->sourceUnit = $sourceUnit;

        foreach ($transformedTypes as $transformedType) {
            $this->addType($transformedType);
        }

        $this->serializer = $serializer;
    }

    /**
     * @return SourceUnit
     */
    public function sourceUnit()
    {
        return $this->sourceUnit;
    }

    /**
     * @return Type[]
     */
    public function transformedTypes()
    {
        return $this->transformedTypes;
    }

    /**
     * @return TypeSerializer
     */
    public function serializer()
    {
        return $this->serializer;
    }

    /**
     * @param Type $type
     */
    private function addType(Type $type)
    {
        $this->transformedTypes[] = $type;
    }
}