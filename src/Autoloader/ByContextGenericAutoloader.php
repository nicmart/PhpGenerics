<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Autoloader;


use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\Loader\ParametrizedTypeLoader;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Type;

/**
 * Class ByContextGenericAutoloader
 * @package NicMart\Generics\Autoloader
 */
class ByContextGenericAutoloader
{
    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * @var ParametrizedTypeLoader
     */
    private $parametrizedTypeLoader;

    /**
     * GenAutoloader constructor.
     * @param TypeParser $typeParser
     * @param ParametrizedTypeLoader $parametrizedTypeLoader
     */
    public function __construct(
        TypeParser $typeParser,
        ParametrizedTypeLoader $parametrizedTypeLoader
    ) {
        $this->typeParser = $typeParser;
        $this->parametrizedTypeLoader = $parametrizedTypeLoader;
    }

    /**
     * @param $className
     * @param NamespaceContext $namespaceContext
     */
    public function autoload($className, NamespaceContext $namespaceContext)
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return;
        }

        // @todo to improve efficiency, we should do a simple string check instead
        // of parsing everytime all the types

        $type = $this->typeParser->parse(
            FullName::fromString($className),
            $namespaceContext
        );

        // If it happens we are autoloading a generic type, it means
        // it is a another generic with variables renamed. We need to generate
        // the code as if it was a ParametrizedType
        if ($type instanceof GenericType) {
            $type = new ParametrizedType(
                $type->name(),
                $type->parameters()
            );
        }

        if (!$type instanceof ParametrizedType) {
            return;
        }

        $compilationResult = $this->parametrizedTypeLoader->load($type);

        foreach ($compilationResult->transformedTypes() as $type) {
            $typeName = $compilationResult
                ->serializer()
                ->serialize($type)
                ->toString()
            ;

            if (class_exists($typeName, false)) {
                continue;
            }

            $this->autoload(
                "\\" . $typeName,
                $this->contextOfType($type, $compilationResult->serializer())
            );
        }
    }

    /**
     * @param Type $type
     * @param TypeSerializer $typeSerializer
     * @return NamespaceContext
     */
    private function contextOfType(Type $type, TypeSerializer $typeSerializer)
    {
        $types = [];
        if ($type instanceof ParametrizedType) {
            $types = $type->arguments();
        }

        $uses = [];

        foreach ($types as $type) {
            $uses[] = new Use_($typeSerializer->serialize($type));
        }

        return new NamespaceContext(
            Namespace_::globalNamespace(),
            new Uses($uses)
        );
    }
}