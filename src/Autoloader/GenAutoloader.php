<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Autoloader;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\Loader\ParametrizedTypeLoader;

class GenAutoloader
{
    /**
     * @var NamespaceContextExtractor
     */
    private $namespaceContextExtractor;
    
    /**
     * @var TypeParser
     */
    private $typeParser;
    
    /**
     * @var ParametrizedTypeLoader
     */
    private $parametrizedTypeLoader;

    /**
     * GenAutoloader constructor.
     * @param NamespaceContextExtractor $namespaceContextExtractor
     * @param TypeParser $typeParser
     * @param ParametrizedTypeLoader $parametrizedTypeLoader
     */
    public function __construct(
        NamespaceContextExtractor $namespaceContextExtractor,
        TypeParser $typeParser,
        ParametrizedTypeLoader $parametrizedTypeLoader
    ) {
        $this->namespaceContextExtractor = $namespaceContextExtractor;
        $this->typeParser = $typeParser;
        $this->parametrizedTypeLoader = $parametrizedTypeLoader;
    }

    public function autoload($className, $fileName)
    {
        $namespaceContext = $this->namespaceContextExtractor->contextOf(
            file_get_contents($fileName)
        );

        $type = $this->typeParser->parse(
            FullName::fromString($className),
            $namespaceContext
        );

        if (!$type instanceof ParametrizedType) {
            return;
        }
        
        $this->parametrizedTypeLoader->load($type);
    }
}