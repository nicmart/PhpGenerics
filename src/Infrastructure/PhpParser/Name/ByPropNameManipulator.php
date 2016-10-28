<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Name;

use PhpParser\Node;

/**
 * Class ByPropNameManipulator
 * @package NicMart\Generics\Infrastructure\PhpParser\Name
 */
class ByPropNameManipulator implements NameManipulator
{
    /**
     * @var string
     */
    private $property;

    /**
     * ByPropNameManipulator constructor.
     * @param $property
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * @param Node $node
     * @return Node\Name
     */
    public function readName(Node $node)
    {
        return $node->{$this->property};
    }

    /**
     * @param Node $node
     * @param Node\Name $name
     * @return Node
     */
    public function withName(Node $node, Node\Name $name)
    {
        $node->{$this->property} = $name;

        return $node;
    }

    /**
     * @param Node $node
     * @return bool
     */
    public function accept(Node $node)
    {
        return property_exists($node, $this->property);
    }
}