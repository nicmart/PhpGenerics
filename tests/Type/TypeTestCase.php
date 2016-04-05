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
                RelativeType::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("\\")
                ),
                Type::fromString("T")
            ),
            array(
                RelativeType::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2")
                ),
                Type::fromString("Ns1\\Ns2\\T")
            ),
            array(
                RelativeType::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2"),
                    array(
                        Use_::fromStrings("A\\B\\T")
                    )
                ),
                Type::fromString("A\\B\\T")
            ),
            array(
                RelativeType::fromString("T"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2"),
                    array(
                        Use_::fromStrings("A\\B\\C", "T")
                    )
                ),
                Type::fromString("A\\B\\C")
            ),
            array(
                RelativeType::fromString("Root\\A\\B"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2"),
                    array(
                        Use_::fromStrings("D\\E", "Root")
                    )
                ),
                Type::fromString("D\\E\\A\\B")
            ),
            array(
                RelativeType::fromString("C"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2\\Ns3"),
                    array(
                        Use_::fromStrings("Ns1", "A"),
                        Use_::fromStrings("Ns1\\Ns2", "B"),
                    )
                ),
                Type::fromString("Ns1\\Ns2\\Ns3\\C")
            ),
            array(
                RelativeType::fromString("C\\D"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1\\Ns2\\Ns3"),
                    array(
                        Use_::fromStrings("Ns1", "A"),
                        Use_::fromStrings("Ns1\\Ns2", "B"),
                    )
                ),
                Type::fromString("Ns1\\Ns2\\Ns3\\C\\D")
            ),
            array(
                RelativeType::fromString("B\\Ns3\\C"),
                new NamespaceContext(
                    Namespace_::fromString("M1\\M2"),
                    array(
                        Use_::fromStrings("Ns1", "A"),
                        Use_::fromStrings("Ns1\\Ns2", "B"),
                    )
                ),
                Type::fromString("Ns1\\Ns2\\Ns3\\C")
            ),
            array(
                RelativeType::fromString("string"),
                new NamespaceContext(
                    Namespace_::fromString("Ns1")
                ),
                Type::fromString("string")
            ),
        );
    }
}