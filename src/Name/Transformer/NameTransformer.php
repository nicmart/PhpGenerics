<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 18/04/2016, 17:14
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;


/**
 * Interface NameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
interface NameTransformer
{
    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return Name
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    );
}