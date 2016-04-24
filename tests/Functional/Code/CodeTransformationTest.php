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
use NicMart\Generics\AST\Name\FullNamePhpParserNameTransformer;
use NicMart\Generics\AST\Visitor\AddUsesVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\PhpDocTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeDefinitionTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeUsageTransformerVisitor;
use NicMart\Generics\AST\PhpDoc\ReplaceTypePhpDocTransformer;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Transformer\ByFullNameNameTransformer;
use NicMart\Generics\Name\Transformer\SimplifierNameTransformer;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

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
        $code = file_get_contents(
            __DIR__ . "/../../../src/Example/Option/Option«T».php"
        );

        $parser = new Parser(new Lexer());

        $statements = $parser->parse($code);

        //var_dump($statements);


        $typeUsageAssignment = NameAssignmentContext::fromStrings(array(
            '\NicMart\Generics\Variable\T' => '\MyNamespace\MyClass'
        ));

        $typeDefAssignments = SimpleNameAssignmentContext::fromStrings(array(
            'Option«T»' => 'Option«MyClass»'
        ));

        $traverser = new NodeTraverser();

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new NamespaceContextVisitor())
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new TypeUsageTransformerVisitor(
                new ByFullNameNameTransformer($typeUsageAssignment)
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
                    new ByFullNameNameTransformer($typeUsageAssignment),
                    new PhpParserDocToPhpdoc(),
                    new Serializer()
                )
            ))
        );

        $traverser->traverse($statements);

        $traverser = new NodeTraverser();

        $use = Use_::fromStrings('\MyNamespace\MyClass');

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new AddUsesVisitor(array(
                $use
            )))
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new NamespaceContextVisitor())
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new TypeUsageTransformerVisitor(
                new SimplifierNameTransformer($use)
            ))
        );

        $traverser->addVisitor(
            new PhpParserVisitorAdapter(new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    new SimplifierNameTransformer($use),
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