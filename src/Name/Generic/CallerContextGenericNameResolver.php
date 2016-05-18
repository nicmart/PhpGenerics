<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic;


use NicMart\Generics\Infrastructure\Source\CallerFilenameResolver;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;

/**
 * Class CallerContextGenericNameResolver
 * @package NicMart\Generics\Name\Generic
 */
class CallerContextGenericNameResolver implements GenericNameResolver
{
    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * @var CallerFilenameResolver
     */
    private $filenameResolver;

    /**
     * @var NamespaceContextExtractor
     */
    private $namespaceContextExtractor;

    /**
     * CallerContextGenericNameResolver constructor.
     * @param GenericNameFactory $genericNameFactory
     * @param CallerFilenameResolver $filenameResolver
     * @param NamespaceContextExtractor $namespaceContextExtractor
     */
    public function __construct(
        GenericNameFactory $genericNameFactory,
        CallerFilenameResolver $filenameResolver,
        NamespaceContextExtractor $namespaceContextExtractor
    ) {
        $this->genericNameFactory = $genericNameFactory;
        $this->filenameResolver = $filenameResolver;
        $this->namespaceContextExtractor = $namespaceContextExtractor;
    }

    /**
     * @param GenericNameInterface $appliedGenericName
     * @return GenericNameInterface
     */
    public function resolve(GenericNameInterface $appliedGenericName)
    {
        $contextOfCaller = $this->namespaceContextOfCaller();

        foreach ($contextOfCaller->uses()->getUsesByAliases() as $use) {
            $useGeneric = $this->genericNameFactory->toGeneric($use->name());

            if (
                $useGeneric->mainName() == $appliedGenericName->mainName()
                && $useGeneric->name() != $appliedGenericName->name()
            ) {
                return $useGeneric;
            }
        }

        throw new \RuntimeException(sprintf(
            "Unable to resolve generic class for %s",
            $appliedGenericName->name()->toString()
        ));
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