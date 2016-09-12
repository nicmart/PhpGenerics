<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Type;

use PhpParser\Node;

/**
 * Class NodeNameTypeAdapter
 *
 * Deals with the inner details of php-parser name handling
 *
 * @package NicMart\Generics\AST\Type
 */
class NodeNameTypeAdapter
{
    /**
     * @var array
     */
    private $props = ["name", "type", "trait", "class"];

    /**
     * @param Node $node
     * @return Node\Name|null
     */
    public function typeNameOf(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            return null;
        }

        foreach ($this->props as $prop) {
            if (isset($node->$prop) && $node->$prop instanceof Node\Name) {
                return $node->$prop;
            }
        }

        if ($node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Interface_
            || $node instanceof Node\Stmt\Trait_
        ) {
            return new Node\Name\Relative($node->name);
        }

        return null;
    }

    /**
     * @param Node $node
     * @param Node\Name $name
     * @return Node
     */
    public function withTypeName(Node $node, Node\Name $name)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            return $node;
        }

        // Alias handling for use uses
        if ($node instanceof Node\Stmt\UseUse) {
            $oldName = $node->name;
            $node->name = $name;
            if ($node->alias == $oldName->getLast()) {
                $node->alias = $name->getLast();
            }
        }

        foreach ($this->props as $prop) {
            if (isset($node->$prop) && $node->$prop instanceof Node\Name) {
                $node->$prop = $name;
                return $node;
            }
        }

        if ($node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Interface_
            || $node instanceof Node\Stmt\Trait_
        ) {
            $node->name = $name->getLast();
            return $node;
        }

        return $node;
    }
}