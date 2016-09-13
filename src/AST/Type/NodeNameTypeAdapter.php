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
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\UseUse;

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
     * @return Name|null
     */
    public function typeNameOf(Node $node)
    {
        if ($node instanceof Namespace_) {
            return null;
        }

        if ($node instanceof UseUse) {
            return new FullyQualified($node->name->parts);
        }

        foreach ($this->props as $prop) {
            if (isset($node->$prop) && $node->$prop instanceof Name) {
                return $node->$prop;
            }
        }

        if ($node instanceof Class_
            || $node instanceof Interface_
            || $node instanceof Trait_
        ) {
            return new Node\Name\Relative($node->name);
        }

        return null;
    }

    /**
     * @param Node $node
     * @param Name $name
     * @return Node
     */
    public function withTypeName(Node $node, Name $name)
    {
        if ($node instanceof Namespace_) {
            return $node;
        }

        // Alias handling for use uses
        if ($node instanceof UseUse) {
            $oldName = $node->name;
            $node->name = $name;
            if ($node->alias == $oldName->getLast()) {
                $node->alias = $name->getLast();
            }
        }

        foreach ($this->props as $prop) {
            if (isset($node->$prop) && $node->$prop instanceof Name) {
                $node->$prop = $name;
                return $node;
            }
        }

        if ($node instanceof Class_
            || $node instanceof Interface_
            || $node instanceof Trait_
        ) {
            $node->name = $name->getLast();
            return $node;
        }

        if ($node instanceof Name) {
            return $name;
        }

        return $node;
    }
}