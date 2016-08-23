<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Transformer\TypeTransformer;

/**
 * Class TypeNameTransformer
 * @package NicMart\Generics\Name\Transformer
 *
 * Transform a TypeTransformer to a NameTransformer
 *
 * NameTransformer(name) := TypeSerializer(TypeTransformer(TypeParser(name)))
 */
final class TypeNameTransformer implements NameTransformer
{
    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * @var TypeTransformer
     */
    private $typeTransformer;

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * TypeNameTransformer constructor.
     * @param TypeParser $typeParser
     * @param TypeTransformer $typeTransformer
     * @param TypeSerializer $typeSerializer
     */
    public function __construct(
        TypeParser $typeParser,
        TypeTransformer $typeTransformer,
        TypeSerializer $typeSerializer
    ) {

        $this->typeParser = $typeParser;
        $this->typeTransformer = $typeTransformer;
        $this->typeSerializer = $typeSerializer;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return FullName
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        $fullname = $namespaceContext->qualify($name);

        return $this->typeSerializer->serialize(
            $this->typeTransformer->transform(
                $this->typeParser->parse($fullname, $namespaceContext)
            )
        );
    }
}