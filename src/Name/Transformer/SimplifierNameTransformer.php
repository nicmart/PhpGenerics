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

        // @todo I think this is showing a modeling problem in simplifiers
        // It's WRONG returning a relative name that is the same as the fullname
        // when there is no possilbe semplification

        $simplified = $this->nameSimplifier->simplify($fullName);

        if ($simplified->parts() == $fullName->parts()) {
            return $name;
        }

        return $simplified;
    }
}