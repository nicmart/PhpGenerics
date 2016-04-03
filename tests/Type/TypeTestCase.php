<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Use_;
use PHPUnit_Framework_TestCase;

class TypeTestCase extends PHPUnit_Framework_TestCase
{
    public function data()
    {
        return array(
            array(
                new RelativeType("T"),
                new NamespaceContext(
                    new Namespace_("\\")
                ),
                new Type("T")
            ),
            array(
                new RelativeType("T"),
                new NamespaceContext(
                    new Namespace_("Ns1\\Ns2")
                ),
                new Type("Ns1\\Ns2\\T")
            ),
            array(
                new RelativeType("T"),
                new NamespaceContext(
                    new Namespace_("Ns1\\Ns2"),
                    array(
                        new Use_("A\\B\\T")
                    )
                ),
                new Type("A\\B\\T")
            ),
            array(
                new RelativeType("T"),
                new NamespaceContext(
                    new Namespace_("Ns1\\Ns2"),
                    array(
                        new Use_("A\\B\\C", "T")
                    )
                ),
                new Type("A\\B\\C")
            ),
            array(
                new RelativeType("Root\\A\\B"),
                new NamespaceContext(
                    new Namespace_("Ns1\\Ns2"),
                    array(
                        new Use_("D\\E", "Root")
                    )
                ),
                new Type("D\\E\\A\\B")
            ),
            array(
                new RelativeType("C"),
                new NamespaceContext(
                    new Namespace_("Ns1\\Ns2\\Ns3"),
                    array(
                        new Use_("Ns1", "A"),
                        new Use_("Ns1\\Ns2", "B"),
                    )
                ),
                new Type("Ns1\\Ns2\\Ns3\\C")
            ),
            array(
                new RelativeType("C\\D"),
                new NamespaceContext(
                    new Namespace_("Ns1\\Ns2\\Ns3"),
                    array(
                        new Use_("Ns1", "A"),
                        new Use_("Ns1\\Ns2", "B"),
                    )
                ),
                new Type("Ns1\\Ns2\\Ns3\\C\\D")
            ),
            array(
                new RelativeType("B\\Ns3\\C"),
                new NamespaceContext(
                    new Namespace_("M1\\M2"),
                    array(
                        new Use_("Ns1", "A"),
                        new Use_("Ns1\\Ns2", "B"),
                    )
                ),
                new Type("Ns1\\Ns2\\Ns3\\C")
            ),
            array(
                new RelativeType("string"),
                new NamespaceContext(
                    new Namespace_("Ns1")
                ),
                new Type("string")
            ),
        );
    }
}