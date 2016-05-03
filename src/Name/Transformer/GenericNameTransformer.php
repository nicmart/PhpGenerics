<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Name;

/**
 * Class GenericNameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
class GenericNameTransformer implements NameTransformer
{
    /**
     * @var FullNameTransformer
     */
    private $innerNameTransformer;

    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * GenericNameTransformer constructor.
     * @param NameTransformer $innerNameTransformer
     * @param GenericNameFactory $genericNameFactory
     */
    public function __construct(
        NameTransformer $innerNameTransformer,
        GenericNameFactory $genericNameFactory
    ) {
        $this->innerNameTransformer = $innerNameTransformer;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return FullName|Name
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        $fullName = $name instanceof FullName
            ? $name
            : $namespaceContext->qualify($name)
        ;

        if (!$this->genericNameFactory->isGeneric($fullName)) {
            return $name;
        }

        $genericName = $this->genericNameFactory->toGeneric($fullName);
        $typeVars = $genericName->parameters($namespaceContext);

        $typeValues = array();

        foreach ($typeVars as $typeVar) {
            $typeValues[] = $this->innerNameTransformer->transformName(
                $typeVar,
                $namespaceContext
            );
        }

        return $namespaceContext->simplify($genericName->apply($typeValues));
    }
}