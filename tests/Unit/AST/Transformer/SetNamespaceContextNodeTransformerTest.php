<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer;


use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class SetNamespaceContextNodeTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP5);
        $printer = new Standard();

        $code = <<<CODE
<?php
namespace A\B\C;

use Boo\Bar;
use Oh\My\God;

class A {

}

echo "hi";
CODE;

        $expectedCode = <<<CODE
<?php
namespace F\G;

use Bar\Baz as Alias;

class A {

}

echo "hi";
CODE;

        $newContext = new NamespaceContext(
            Namespace_::fromString("F\\G"), new Uses([
                Use_::fromStrings("Bar\\Baz", "Alias")
            ])
        );

        $transformer = new SetNamespaceContextNodeTransformer(
            $newContext,
            new PhpNameAdapter()
        );

        $this->assertEquals(
            $this->normaliseCode($this->normaliseCode($expectedCode)),
            $this->normaliseCode("<?php\n" . $printer->prettyPrint(
                $transformer->transformNodes($parser->parse($code))
            ))
        );
    }

    private function normaliseCode($code)
    {
        $code = trim($code);
        $parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP5);
        $printer = new Standard();

        return "<?php\n" . $printer->prettyPrint($parser->parse($code));
    }
}
