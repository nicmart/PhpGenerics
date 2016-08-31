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
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\Loader\ParametrizedTypeLoader;

/**
 * Class ByFileGenericAutoloader
 *
 * Autoload a class having the information of the caller file path
 *
 * @package NicMart\Generics\Autoloader
 */
class ByFileGenericAutoloader
{
    /**
     * @var NamespaceContextExtractor
     */
    private $namespaceContextExtractor;

    /**
     * @var ByContextGenericAutoloader
     */
    private $byContextGenericAutoloader;

    /**
     * GenAutoloader constructor.
     * @param NamespaceContextExtractor $namespaceContextExtractor
     * @param ByContextGenericAutoloader $byContextGenericAutoloader
     */
    public function __construct(
        NamespaceContextExtractor $namespaceContextExtractor,
        ByContextGenericAutoloader $byContextGenericAutoloader
    ) {
        $this->namespaceContextExtractor = $namespaceContextExtractor;
        $this->byContextGenericAutoloader = $byContextGenericAutoloader;
    }

    /**
     * @param $className
     * @param $callerFilename
     */
    public function autoload($className, $callerFilename)
    {
        $namespaceContext = $this->namespaceContextExtractor->contextOf(
            file_get_contents($callerFilename)
        );

        $this->byContextGenericAutoloader->autoload(
            $className,
            $namespaceContext
        );
    }
}