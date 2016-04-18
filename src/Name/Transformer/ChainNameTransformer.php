<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;

/**
 * Class ChainPhpParserNameTransformer
 * @package NicMart\Generics\AST\Name
 */
class ChainNameTransformer implements NameTransformer
{
    /**
     * @var NameTransformer[]
     */
    private $nameTransformers = array();

    /**
     * ChainPhpParserNameTransformer constructor.
     *
     * @param NameTransformer[] $nameTransformers
     */
    public function __construct(array $nameTransformers)
    {
        foreach ($nameTransformers as $nameTransformer) {
            $this->addNameTransformer($nameTransformer);
        }
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     *
     * @return Name
     */
    public function transformName(Name $name, NamespaceContext $namespaceContext)
    {
        $transformedName = $name;

        foreach ($this->nameTransformers as $nameTransformer) {
            $transformedName = $nameTransformer->transformName(
                $transformedName,
                $namespaceContext
            );
        }

        return $transformedName;
    }


    /**
     * @param NameTransformer $phpParserNameTransformer
     */
    private function addNameTransformer(
        NameTransformer $phpParserNameTransformer
    ) {
        $this->nameTransformers[] = $phpParserNameTransformer;
    }
}