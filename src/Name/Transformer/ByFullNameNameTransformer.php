<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 18/04/2016, 17:29
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Transformer;

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;

/**
 * Class ByFullNameNameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
class ByFullNameNameTransformer implements NameTransformer
{
    /**
     * @var FullNameTransformer
     */
    private $fullNameTransformer;

    /**
     * ByFullNameNameTransformer constructor.
     * @param FullNameTransformer $fullNameTransformer
     */
    public function __construct(FullNameTransformer $fullNameTransformer)
    {
        $this->fullNameTransformer = $fullNameTransformer;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return FullName
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        $fullName = $name instanceof RelativeName
            ? $namespaceContext->qualify($name)
            : $name
        ;

        return $this->fullNameTransformer->transform($fullName);
    }
}