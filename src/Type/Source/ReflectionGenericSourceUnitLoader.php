<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Source;

use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use ReflectionClass;

class ReflectionGenericSourceUnitLoader implements GenericSourceUnitLoader
{
    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * ReflectionGenericSourceUnitLoader constructor.
     * @param TypeSerializer $typeSerializer
     */
    public function __construct(TypeSerializer $typeSerializer)
    {
        $this->typeSerializer = $typeSerializer;
    }

    /**
     * @param GenericType $genericType
     * @return SourceUnit
     */
    public function loadSource(GenericType $genericType)
    {
        $fullName = $this->typeSerializer->serialize($genericType);
        $reflection = new ReflectionClass($fullName->toString());

        return new SourceUnit(
            $genericType,
            $fullName,
            file_get_contents($reflection->getFileName())
        );
    }
}