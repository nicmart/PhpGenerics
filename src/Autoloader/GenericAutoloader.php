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
use NicMart\Generics\Name\Generic\AngleQuotedGenericNameInterface;
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
     * GenericAutoloader constructor.
     * @param DefaultGenericCompiler $compiler
     * @param SourceUnitEvaluation $evaluation
     * @param CallerFilenameResolver $filenameResolver
     * @param NamespaceContextExtractor $namespaceContextExtractor
     * @param GenericNameResolver $genericNameResolver
     * @param GenericNameFactory $genericNameFactory
     */
    public function __construct(
        DefaultGenericCompiler $compiler,
        SourceUnitEvaluation $evaluation,
        CallerFilenameResolver $filenameResolver,
        NamespaceContextExtractor $namespaceContextExtractor,
        GenericNameResolver $genericNameResolver,
        GenericNameFactory $genericNameFactory
    ) {
        $this->compiler = $compiler;
        $this->evaluation = $evaluation;
        $this->filenameResolver = $filenameResolver;
        $this->namespaceContextExtractor = $namespaceContextExtractor;
        $this->genericNameResolver = $genericNameResolver;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param $className
     * @return bool|void
     */
    public function __invoke($className)
    {
        $name = FullName::fromString($className);

        if (!$this->genericNameFactory->isGeneric($name)) {
            return;
        }

        $namespaceContext = $this->namespaceContextOfCaller();

        $appliedGeneric = $this->genericNameFactory->toGeneric(
            $name,
            $namespaceContext
        );

        $genericParams = $appliedGeneric->parameters();

        $generic = $this->genericNameResolver->resolve($appliedGeneric);

        $source = $this->compiler->compile(
            $generic,
            $genericParams
        );

        $this->evaluation->evaluate($source);

        return true;
    }

    /**
     * @return NamespaceContext
     */
    private function namespaceContextOfCaller()
    {
        $callerFilename = $this->filenameResolver->filename(array(__FILE__));

        return $this->namespaceContextExtractor->contextOf(
            file_get_contents($callerFilename)
        );
    }
}