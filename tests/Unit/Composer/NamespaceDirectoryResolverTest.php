<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Composer;


use Composer\Autoload\ClassLoader;

class NamespaceDirectoryResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var NamespaceDirectoryResolver
     */
    private $resolver;

    public function setUp()
    {
        $this->classLoader = new ClassLoader();

        $this->resolver = new NamespaceDirectoryResolver($this->classLoader);
    }

    /**
     * @test
     */
    public function it_resolves_psr4()
    {
        $this->classLoader->setPsr4(
            "A\\B\\",
            array("/dir1/", "/dir2/")
        );

        $this->classLoader->setPsr4(
            "",
            array("/fall1/", "/fall2/")
        );

        $this->assertEquals(
            array(
                "/dir1/C",
                "/dir2/C",
                "/fall1/A/B/C",
                "/fall2/A/B/C",
            ),
            $this->resolver->directories("A\\B\\C")
        );
    }

    /**
     * @test
     */
    public function it_resolves_psr0()
    {
        $this->classLoader->set(
            "C\\D\\",
            array("/dir3/", "/dir4/")
        );

        $this->classLoader->set(
            "",
            array("/fall3/", "/fall4/")
        );

        $this->assertEquals(
            array(
                "/dir3/C/D/E",
                "/dir4/C/D/E",
                "/fall3/C/D/E",
                "/fall4/C/D/E",
            ),
            $this->resolver->directories("C\\D\\E")
        );
    }
}
