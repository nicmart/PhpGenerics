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
use PhpParser\Node\Stmt\UseUse;

class NodeNameTypeAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeNameTypeAdapter
     */
    private $adapter;

    public function setUp()
    {
        $this->adapter = new NodeNameTypeAdapter();
    }

    /**
     * @dataProvider data
     * @param Name $name1
     * @param Name $name2
     * @param Node $node1
     * @param Node $node2
     */
    public function testNameOfAndWithName(
        Name $name1 = null,
        Name $name2 = null,
        Node $node1,
        Node $node2
    ) {
        $this->assertEquals(
            $name1,
            $this->adapter->typeNameOf($node1)
        );

        $this->assertEquals(
            $node2,
            $this->adapter->withTypeName($node1, $name2)
        );
    }

    public function data()
    {
        $name1 = new Name("foo\\bar");
        $name2 = new Name("mah\\boh");

        return [
            [
                // We correct the name making it fully qualified
                new Name\FullyQualified($name1->parts),
                $name2,
                new UseUse($name1),
                new UseUse($name2)
            ],
            [
                new Name\FullyQualified($name1->parts),
                $name2,
                new UseUse($name1, "ah"),
                new UseUse($name2, "ah")
            ],
            [
                $name1,
                $name2,
                new Node\Expr\ConstFetch($name1),
                new Node\Expr\ConstFetch($name2)
            ],
            [
                $name1,
                $name2,
                new Node\Expr\ClassConstFetch($name1, "foo"),
                new Node\Expr\ClassConstFetch($name2, "foo")
            ],
            [
                $name1,
                $name2,
                new Node\Expr\StaticPropertyFetch($name1, "foo"),
                new Node\Expr\StaticPropertyFetch($name2, "foo")
            ],
            [
                $name1,
                $name2,
                new Node\Stmt\Catch_($name1, "ah"),
                new Node\Stmt\Catch_($name2, "ah")
            ],
            [
                null,
                $name2,
                new Node\Stmt\Namespace_($name1), //Namespace must remain unaltered
                new Node\Stmt\Namespace_($name1),
            ],
            [
                new Name\Relative("MyClass"),
                new Name\Relative("YourClass"),
                new Node\Stmt\Class_("MyClass", []),
                new Node\Stmt\Class_("YourClass", []),
            ],
            [
                new Name\Relative("MyClass"),
                new Name\Relative("YourClass"),
                new Node\Stmt\Interface_("MyClass", []),
                new Node\Stmt\Interface_("YourClass", []),
            ],
            [
                new Name\Relative("MyClass"),
                new Name\Relative("YourClass"),
                new Node\Stmt\Trait_("MyClass", []),
                new Node\Stmt\Trait_("YourClass", []),
            ],
            [
                $name1,
                $name2,
                new Node\Stmt\TraitUseAdaptation\Alias($name1, "ah", null, null),
                new Node\Stmt\TraitUseAdaptation\Alias($name2, "ah", null, null),
            ],
            [
                $name1,
                $name2,
                new Node\Expr\Instanceof_(new Node\Scalar\String_("foo"), $name1),
                new Node\Expr\Instanceof_(new Node\Scalar\String_("foo"), $name2)
            ],
        ];
    }
}
