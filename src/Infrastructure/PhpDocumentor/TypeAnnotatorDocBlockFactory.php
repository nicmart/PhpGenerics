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


use NicMart\Generics\Type\Parser\TypeParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types;

/**
 * Class TypeAnnotatorBlockFactory
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
class TypeAnnotatorDocBlockFactory
{
    /**
     * @param TypeParser $typeParser
     * @param array $additionalTags
     * @return DocBlockFactory
     */
    public static function createInstance(
        TypeParser $typeParser,
        array $additionalTags = []
    ) {
        $fqsenResolver = new FqsenResolver();
        $tagFactory = new StandardTagFactory($fqsenResolver);
        $descriptionFactory = new DescriptionFactory($tagFactory);

        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new TypeResolver($fqsenResolver));

        $tagAnnotatorFactory = new TypeAnnotatorTagFactory(
            $tagFactory,
            $typeParser
        );

        $docBlockFactory = new DocBlockFactory($descriptionFactory, $tagAnnotatorFactory);
        foreach ($additionalTags as $tagName => $tagHandler) {
            $docBlockFactory->registerTagHandler($tagName, $tagHandler);
        }

        return $docBlockFactory;
    }
}