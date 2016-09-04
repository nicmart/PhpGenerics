<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Loader;


use NicMart\Generics\Type\Compiler\CompilationResult;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Type;

/**
 * Class RecursiveParametrizedTypeLoader
 * @package NicMart\Generics\Type\Loader
 */
class RecursiveParametrizedTypeLoader implements ParametrizedTypeLoader
{
    /**
     * @var ParametrizedTypeLoader
     */
    private $typeLoader;

    /**
     * RecursiveParametrizedTypeLoader constructor.
     * @param ParametrizedTypeLoader $typeLoader
     */
    public function __construct(ParametrizedTypeLoader $typeLoader)
    {
        $this->typeLoader = $typeLoader;
    }

    /**
     * @param ParametrizedType $parametrizedType
     * @return CompilationResult|null
     */
    public function load(ParametrizedType $parametrizedType)
    {
        $result = $this->typeLoader->load($parametrizedType);

        if (!$result) {
            return;
        }

        $serializer = $result->serializer();

        foreach ($result->transformedTypes() as $type) {
            if (!$type instanceof ParametrizedType && !$type instanceof GenericType) {
                continue;
            }

            if ($this->hasTypeBeenLoaded($type, $serializer)) {
                continue;
            }

            if ($type instanceof GenericType) {
                $type = $type->toParametrizedType();
            }

            $this->load($type);
        }
    }

    /**
     * @param Type $type
     * @param TypeSerializer $serializer
     * @return bool
     */
    private function hasTypeBeenLoaded(Type $type, TypeSerializer $serializer)
    {
        $typeName = $serializer->serialize($type)->toString();

        return class_exists($typeName, false) || interface_exists($typeName, false);
    }
}