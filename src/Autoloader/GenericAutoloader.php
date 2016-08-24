<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Autoloader;

use NicMart\Generics\Infrastructure\Source\CallerFilenameResolver;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Generic\GenericNameResolver;
use NicMart\Generics\Source\Compiler\DefaultGenericCompiler;
use NicMart\Generics\Source\Evaluation\SourceUnitEvaluation;

/**
 * Class GenericAutoloader
 * @package NicMart\Generics\Autoloader
 */
class GenericAutoloader
{
    /**
     * @var DefaultGenericCompiler
     */
    private $compiler;

    /**
     * @var SourceUnitEvaluation
     */
    private $evaluation;

    /**
     * @var CallerFilenameResolver
     */
    private $filenameResolver;

    /**
     * @var NamespaceContextExtractor
     */
    private $namespaceContextExtractor;

    /**
     * @var GenericNameResolver
     */
    private $genericNameResolver;
    
    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * @var ByFileGenericAutoloader
     */
    private $byFileGenericAutoloader;

    /**
     * GenericAutoloader constructor.
     * @param ByFileGenericAutoloader $byFileGenericAutoloader
     * @param CallerFilenameResolver $filenameResolver
     */
    public function __construct(
        ByFileGenericAutoloader $byFileGenericAutoloader,
        CallerFilenameResolver $filenameResolver
    ) {
        $this->byFileGenericAutoloader = $byFileGenericAutoloader;
        $this->filenameResolver = $filenameResolver;
    }

    /**
     * @param $className
     * @return bool|void
     */
    public function __invoke($className)
    {
        $this->byFileGenericAutoloader->autoload(
            $className,
            $this->callerFilename()
        );

        return true;
    }

    /**
     * @return NamespaceContext
     */
    private function callerFilename()
    {
        return $this->filenameResolver->filename(array(__FILE__));
    }
}