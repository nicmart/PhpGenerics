<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 18/04/2016, 17:40
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;

/**
 * Class SimplifierNameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
class SimplifierNameTransformer implements NameTransformer
{
    /**
     * @var NameSimplifier
     */
    private $nameSimplifier;

    /**
     * SimplifierNameTransformer constructor.
     * @param NameSimplifier $nameSimplifier
     */
    public function __construct(NameSimplifier $nameSimplifier)
    {
        $this->nameSimplifier = $nameSimplifier;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     *
     * @return Name
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        $fullName = $name instanceof RelativeName
            ? $namespaceContext->qualify($name)
            : $name
        ;

        $simplified =  $this->nameSimplifier->simplify($fullName);

        if ($simplified == $fullName) {
            return $name;
        }

        return $simplified;
    }
}