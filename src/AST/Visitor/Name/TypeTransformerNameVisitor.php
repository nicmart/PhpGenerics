<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor\Name;

use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\Type\Transformer\TypeTransformer;
use PhpParser\Node;

class TypeTransformerNameVisitor implements NameVisitor
{
    /**
     * @var TypeTransformer
     */
    private $typeTransformer;

    /**
     * TypeTransformerNameVisitor constructor.
     * @param TypeTransformer $typeTransformer
     */
    public function __construct(TypeTransformer $typeTransformer)
    {
        $this->typeTransformer = $typeTransformer;
    }

    /**
     * @param Node\Name $name
     * @return MaintainNode
     */
    public function visitName(Node\Name $name)
    {
        $name->setAttribute(
            TypeAnnotatorNameVisitor::ATTR_NAME,
            $this->typeTransformer->transform(
                $name->getAttribute(TypeAnnotatorNameVisitor::ATTR_NAME)
            )
        );

        return new MaintainNode();
    }
}