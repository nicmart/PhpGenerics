<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor;


use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use PhpParser\Node\Stmt;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\Name\FullName;
use PhpParser\Node;

class RemoveParentTypeVisitor implements Visitor
{
    /**
     * @var FullName[]
     */
    private $names = array();

    /**
     * RemoveParentTypeVisitor constructor.
     * @param FullName[] $names
     */
    public function __construct(array $names)
    {
        foreach ($names as $name) {
            $this->addName($name);
        }
    }

    public function enterNode(Node $node)
    {
        $nsContext = $node->getAttribute(NamespaceContextVisitor::ATTR_NAME);

        if ($node instanceof Stmt\Class_) {
            if ($this->hasNameToBeRemoved($node->extends, $nsContext)) {
                $node->extends = null;
            }

            foreach ($node->implements as $i => $interface) {
                if ($this->hasNameToBeRemoved($interface, $nsContext)) {
                    unset($node->implements[$i]);
                }
            }
        } elseif ($node instanceof Stmt\Interface_) {
            foreach ($node->extends as $i => $interface) {
                if ($this->hasNameToBeRemoved($interface, $nsContext)) {
                    unset($node->extends[$i]);
                }
            }
        }

        return new MaintainNode();
    }

    public function leaveNode(Node $node)
    {
        return new MaintainNode();
    }

    private function addName(FullName $name)
    {
        $this->names[$name->toString()] = $name;
    }

    /**
     * @param Node\Name $name
     * @return Name
     */
    private function fromPhpNameToName(Node\Name  $name)
    {
        if ($name->isFullyQualified()) {
            return new FullName($name->parts);
        }

        return new RelativeName($name->parts);
    }

    /**
     * @param Node\Name|null $phpName
     * @param NamespaceContext $context
     * @return bool
     */
    private function hasNameToBeRemoved(
        $phpName,
        NamespaceContext $context
    ) {
        if (!$phpName instanceof Node\Name) {
            return false;
        }

        $name = $context->qualify($this->fromPhpNameToName($phpName));

        return isset($this->names[$name->toString()]);
    }
}