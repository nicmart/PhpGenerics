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


use NicMart\Generics\Name\Name;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Transformer\TypeTransformer;

/**
 * Class TypeNameTransformer
 *
 * Lift a type-transformer to a name-transformer
 *
 * @package NicMart\Generics\Name\Transformer
 */
class TypeNameTransformer implements NameTransformer
{
    /**
     * @var NameQualifier
     */
    private $nameQualifier;

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * @var TypeTransformer
     */
    private $typeTransformer;

    /**
     * TypeNameTransformer constructor.
     * @param NameQualifier $nameQualifier
     * @param TypeParser $typeParser
     * @param TypeTransformer $typeTransformer
     * @param TypeSerializer $typeSerializer
     */
    public function __construct(
        NameQualifier $nameQualifier,
        TypeParser $typeParser,
        TypeTransformer $typeTransformer,
        TypeSerializer $typeSerializer
    ) {
        $this->nameQualifier = $nameQualifier;
        $this->typeParser = $typeParser;
        $this->typeSerializer = $typeSerializer;
        $this->typeTransformer = $typeTransformer;
    }

    /**
     * @param Name $name
     * @return Name
     */
    public function __invoke(Name $name)
    {
        $fromType = $this->typeParser->parse(
            $name,
            $this->nameQualifier
        );

        $toType = $this->typeTransformer->transform($fromType);

        return $this->typeSerializer->serialize($toType);
    }
}