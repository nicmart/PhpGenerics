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
use NicMart\Generics\Name\Generic\AngleQuotedGenericName;
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
     * GenericAutoloader constructor.
     * @param DefaultGenericCompiler $compiler
     * @param SourceUnitEvaluation $evaluation
     * @param CallerFilenameResolver $filenameResolver
     * @param NamespaceContextExtractor $namespaceContextExtractor
     */
    public function __construct(
        DefaultGenericCompiler $compiler,
        SourceUnitEvaluation $evaluation,
        CallerFilenameResolver $filenameResolver,
        NamespaceContextExtractor $namespaceContextExtractor
    ) {
        $this->compiler = $compiler;
        $this->evaluation = $evaluation;
        $this->filenameResolver = $filenameResolver;
        $this->namespaceContextExtractor = $namespaceContextExtractor;
    }

    /**
     * @param $className
     * @return bool|void
     */
    public function __invoke($className)
    {
        if (!$this->isGenericClassName($className)) {
            return;
        }

        $namespaceContext = $this->namespaceContextOfCaller();

        $appliedGeneric = new AngleQuotedGenericName(
            FullName::fromString($className)
        );

        $genericParams = $appliedGeneric->parameters($namespaceContext);

        $generic = new AngleQuotedGenericName($this->genericName(
            $className,
            $namespaceContext
        ));

        $source = $this->compiler->compile(
            $generic,
            $genericParams
        );

        $this->evaluation->evaluate($source);

        return true;
    }

    /**
     * @param $className
     * @return bool
     */
    private function isGenericClassName($className)
    {
        return strpos($className, "«") !== false;
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

    /**
     * @param string $className
     * @param NamespaceContext $contextOfCaller
     * @return FullName
     */
    private function genericName($className, NamespaceContext $contextOfCaller)
    {
        $mainPartOfClassName = strstr($className, "«", true);

        foreach ($contextOfCaller->uses()->getUsesByAliases() as $use) {
            $name = $use->name();

            $mainPartOfName = strstr(
                $name->toString(),
                "«",
                true
            );

            if (
                $mainPartOfName == $mainPartOfClassName
                && $name->toString() != $className
            ) {
                return $name;
            }
        }

        throw new \RuntimeException(
            "Unable to resolve generic class for $className"
        );
    }
}