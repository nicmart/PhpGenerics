<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\SimpleName;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\UnionType;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context as TypeContext;

class TypeAnnotatorTagFactory implements TagFactory
{
    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * TypeAnnotatorTagFactory constructor.
     * @param TagFactory $tagFactory
     * @param TypeParser $typeParser
     */
    public function __construct(TagFactory $tagFactory, TypeParser $typeParser)
    {
        $this->tagFactory = $tagFactory;
        $this->typeParser = $typeParser;
    }


    /**
     * {@inheritdoc}
     */
    public function addParameter($name, $value)
    {
        $this->tagFactory->addParameter($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addService($service)
    {
        $this->tagFactory->addService($service);
    }

    /**
     * {@inheritdoc}
     */
    public function create($tagLine, TypeContext $context = null)
    {
        $tag = $this->tagFactory->create($tagLine, $context);
        $namespaceContext = $this->toNamespaceContext($context);

        return TagTypeFunctor::map(
            $tag,
            TypeFunctor::bottomUp(
                $this->annotatorMapFunction($namespaceContext)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerTagHandler($tagName, $handler)
    {
        $this->tagFactory->registerTagHandler($tagName, $handler);
    }

    /**
     * Non-recursive function that transform a type to an annotated one
     * @param NamespaceContext $namespaceContext
     * @return \Closure
     */
    private function annotatorMapFunction(NamespaceContext $namespaceContext)
    {
        return function (Type $type) use ($namespaceContext) {
            if ($type instanceof Compound) {
                $domainInnerTypes = [];
                for ($i = 0; $type->has($i); $i++) {
                    /** @var AnnotatedType $innerType */
                    $innerType = $type->get($i);
                    $domainInnerTypes[] = $innerType->type();
                }
                return new AnnotatedType(
                    $type,
                    new UnionType($domainInnerTypes)
                );
            }

            return new AnnotatedType(
                $type,
                $this->typeParser->parse(
                    FullName::fromString((string) $type),
                    $namespaceContext
                )
            );
        };
    }

    /**
     * @param TypeContext $context
     * @return NamespaceContext
     */
    private function toNamespaceContext(TypeContext $context = null)
    {
        if (!$context) {
            return NamespaceContext::emptyContext();
        }

        $namespace = Namespace_::fromString($context->getNamespace());

        $uses = array();

        foreach ($context->getNamespaceAliases() as $alias => $ns) {
            $uses[] = new Use_(
                FullName::fromString($ns),
                new SimpleName($alias)
            );
        }

        return new NamespaceContext(
            $namespace,
            new Uses($uses)
        );
    }
}