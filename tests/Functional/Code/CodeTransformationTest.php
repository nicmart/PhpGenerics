<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Code;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\AngleQuotedGenericName;
use NicMart\Generics\Source\Compiler\GenericCompilerFactory;
use PhpParser\Lexer;

/**
 * Class CodeTransformationTest
 * @package NicMart\Generics\Code
 */
class CodeTransformationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms_php_code()
    {
        $compiler = GenericCompilerFactory::compiler();
        $generic = AngleQuotedGenericName::fromString(
            'NicMart\Generics\Example\Option\Option«T»'
        );

        $innerType = FullName::fromString("Ns\\MyClass1");

        var_dump($compiler->compile(
            $generic,
            array($innerType)
        ));
    }
}