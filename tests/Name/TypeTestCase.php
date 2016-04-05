<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name;

use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use PHPUnit_Framework_TestCase;

class TypeTestCase extends PHPUnit_Framework_TestCase
{
    public function data()
    {
        return array(
            array(
                RelativeName::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("\\")
                ),
                FullName::fromString("T")
            ),
            array(
                RelativeName::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2")
                ),
                FullName::fromString("Ns1\\Ns2\\T")
            ),
            array(
                RelativeName::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2"),
                    array(
                        Use_::fromStrings("A\\B\\T")
                    )
                ),
                FullName::fromString("A\\B\\T")
            ),
            array(
                RelativeName::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2"),
                    array(
                        Use_::fromStrings("A\\B\\C", "T")
                    )
                ),
                FullName::fromString("A\\B\\C")
            ),
            array(
                RelativeName::fromString("Root\\A\\B"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2"),
                    array(
                        Use_::fromStrings("D\\E", "Root")
                    )
                ),
                FullName::fromString("D\\E\\A\\B")
            ),
            array(
                RelativeName::fromString("C"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2\\Ns3"),
                    array(
                        Use_::fromStrings("Ns1", "A"),
                        Use_::fromStrings("Ns1\\Ns2", "B"),
                    )
                ),
                FullName::fromString("Ns1\\Ns2\\Ns3\\C")
            ),
            array(
                RelativeName::fromString("C\\D"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2\\Ns3"),
                    array(
                        Use_::fromStrings("Ns1", "A"),
                        Use_::fromStrings("Ns1\\Ns2", "B"),
                    )
                ),
                FullName::fromString("Ns1\\Ns2\\Ns3\\C\\D")
            ),
            array(
                RelativeName::fromString("B\\Ns3\\C"),
                new NamespaceContext(
                    Namespace_::fromString("M1\\M2"),
                    array(
                        Use_::fromStrings("Ns1", "A"),
                        Use_::fromStrings("Ns1\\Ns2", "B"),
                    )
                ),
                FullName::fromString("Ns1\\Ns2\\Ns3\\C")
            ),
            array(
                RelativeName::fromString("string"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1")
                ),
                FullName::fromString("string")
            ),
        );
    }
}