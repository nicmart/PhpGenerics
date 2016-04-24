<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source;


use NicMart\Generics\Name\FullName;

class ReflectionSourceResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_get_contents_of_source_file()
    {
        $resolver = new ReflectionSourceResolver();
        $filename = __DIR__ . "/class.php";
        $source = <<<EOF
<?php
namespace Ns;

class ABCD {}
EOF;

        file_put_contents($filename, $source);

        include $filename;

        $name = FullName::fromString("Ns\\ABCD");

        $this->assertEquals(
            $source,
            $resolver->sourceOf($name)
        );

        unlink($filename);
    }
}
