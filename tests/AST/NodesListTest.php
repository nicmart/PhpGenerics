<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST;


class NodesListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_gets_nodes()
    {
        $nodeList = new NodesList($nodes = array(
            $this->getNode(),
            $this->getNode()
        ));

        $this->assertSame(
            $nodes,
            $nodeList->nodes()
        );
    }

    /**
     * @test
     */
    public function it_appends()
    {
        $nodes = array(
            $this->getNode(),
            $this->getNode()
        );

        $nodeList = new NodesList($nodes);

        $node = $this->getNode();

        $this->assertEquals(
            new NodesList(array_merge($nodes, array($node))),
            $nodeList->append($node)
        );
    }

    /**
     * @test
     */
    public function it_prepends()
    {
        $nodes = array(
            $this->getNode(),
            $this->getNode()
        );

        $nodeList = new NodesList($nodes);

        $node = $this->getNode();

        $this->assertEquals(
            new NodesList(array_merge(array($node), $nodes)),
            $nodeList->prepend($node)
        );
    }

    /**
     * @test
     */
    public function it_appends_list()
    {
        $nodes1 = array(
            $this->getNode(),
            $this->getNode()
        );

        $nodeList1 = new NodesList($nodes1);

        $nodes2 = array(
            $this->getNode(),
            $this->getNode()
        );

        $nodeList2 = new NodesList($nodes2);

        $this->assertEquals(
            new NodesList(array_merge($nodes1, $nodes2)),
            $nodeList1->appendList($nodeList2)
        );
    }

    /**
     * @test
     */
    public function it_prepends_list()
    {
        $nodes1 = array(
            $this->getNode(),
            $this->getNode()
        );

        $nodeList1 = new NodesList($nodes1);

        $nodes2 = array(
            $this->getNode(),
            $this->getNode()
        );

        $nodeList2 = new NodesList($nodes2);

        $this->assertEquals(
            new NodesList(array_merge($nodes2, $nodes1)),
            $nodeList1->prependList($nodeList2)
        );
    }

    private function getNode()
    {
        static $marker = 0;
        $mock = $this->getMock('\PhpParser\Node');
        $mock->marker = spl_object_hash($mock);

        return $mock;
    }
}
