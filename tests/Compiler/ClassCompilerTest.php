<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Compiler;


use PhpParser\Lexer;
use PhpParser\NodeDumper;
use PhpParser\Parser;

include __DIR__ . "/../../vendor/nikic/php-parser/lib/bootstrap.php";

class ClassCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassCompiler
     */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new ClassCompiler(new Parser(new Lexer()));
    }

    /**
     * @test
     */
    public function it_transforms_method_arguments()
    {
        $code = '<?php
            namespace D;

            use NicMart\Generics\Variable\T;

            use A\B as C;

            interface TestInterface {
                /**
                 * asdasdasdads
                 * @param T $x
                 */
                function foo(A $x);


                private function bar(T $y);

            }
        ';

        $compiled = '<?php
            use Ns\TestClass;

            interface TestInterfaceTestClass {
                function foo(TestClass $x);
                private function bar(TestClass $y);
                protected function baz(TestClass $x, TestClass $y);
                private function f(C $c);
            }
        ';

        $this->assertEquals(
            $compiled,
            $this->compiler->compile($code, array(
                'NicMart\Generics\Variable\T' => 'Ns\TestClass'
            ))
        );

    }
}
