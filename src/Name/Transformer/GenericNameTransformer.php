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

class GenericNameTransformer implements NameTransformer
{
    /**
     * @var FullNameTransformer
     */
    private $innerFullNameTransformer;

    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * GenericNameTransformer constructor.
     * @param FullNameTransformer $innerFullNameTransformer
     * @param GenericNameFactory $genericNameFactory
     */
    public function __construct(
        FullNameTransformer $innerFullNameTransformer,
        GenericNameFactory $genericNameFactory
    ) {
        $this->innerFullNameTransformer = $innerFullNameTransformer;
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
            $typeValues[] = $this->innerFullNameTransformer->transform(
                $typeVar
            );
        }

        return $genericName->apply($typeValues);
    }
}