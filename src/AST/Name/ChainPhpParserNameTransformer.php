<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Name;


use NicMart\Generics\Name\Context\NamespaceContext;
use PhpParser\Node\Name;

/**
 * Class ChainPhpParserNameTransformer
 * @package NicMart\Generics\AST\Name
 */
class ChainPhpParserNameTransformer implements PhpParserNameTransformer
{
    /**
     * @var PhpParserNameTransformer[]
     */
    private $phpParserNameTransformers = array();

    /**
     * ChainPhpParserNameTransformer constructor.
     *
     * @param PhpParserNameTransformer[] $phpParserNameTranasformers
     */
    public function __construct(array $phpParserNameTranasformers)
    {
        foreach ($phpParserNameTranasformers as $nameTranasformer) {
            $this->addNameTransformer($nameTranasformer);
        }
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     *
     * @return Name
     */
    public function transform(Name $name, NamespaceContext $namespaceContext)
    {
        $transformedName = $name;

        foreach ($this->phpParserNameTransformers as $phpParserNameTransformer) {
            $transformedName = $phpParserNameTransformer->transform(
                $transformedName,
                $namespaceContext
            );
        }

        return $transformedName;
    }


    /**
     * @param PhpParserNameTransformer $phpParserNameTransformer
     */
    private function addNameTransformer(
        PhpParserNameTransformer $phpParserNameTransformer
    ) {
        $this->phpParserNameTransformers[] = $phpParserNameTransformer;
    }
}