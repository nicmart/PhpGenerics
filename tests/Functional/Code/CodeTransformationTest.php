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


use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Adapter\PhpParserVisitorAdapter;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\PhpDocTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeDefinitionTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeUsageTransformerVisitor;
use NicMart\Generics\Compiler\PhpDoc\ReplaceTypePhpDocTransformer;
use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\FullName;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterTest;

class CodeTransformationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms_php_code()
    {
        $code = file_get_contents(
            __DIR__ . "/../../../src/Example/Option/Option.php"
        );

        $parser = new Parser(new Lexer());

        $statements = $parser->parse($code);

        var_dump($statements);


        $assignments = NameAssignmentContext::fromStrings(array(
            '\NicMart\Generics\Variable\T' => 'stdClass'
        ));

        $typeDefAssignments = NameAssignmentContext::fromStrings(array(
            '\NicMart\Generics\Example\Option\Option' => 'OptionOfStdClass'
        ));

        $traverser = new NodeTraverser();

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new NamespaceContextVisitor())
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new TypeUsageTransformerVisitor(
                $assignments
            ))
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new TypeDefinitionTransformerVisitor(
                $typeDefAssignments
            ))
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    $assignments,
                    new PhpParserDocToPhpdoc(),
                    new Serializer()
                )
            ))
        );

        $traverser->traverse($statements);

        $prettyPrinter = new Standard();

        var_dump($prettyPrinter->prettyPrint($statements));
    }
}