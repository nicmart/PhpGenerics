<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 20/04/2016, 13:13
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\GenericName;

use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Interface GenericName
 * @package NicMart\Generics\Name\GenericName
 */
interface GenericName
{
    /**
     * @return FullName
     */
    public function name();

    /**
     * @param FullName[] $names
     * @return FullName
     */
    public function apply(array $names);

    /**
     * @param FullName[] $names
     * @param NameQualifier $namespaceContext
     * @return NameAssignmentContext
     *
     */
    public function assignments(
        array $names,
        NameQualifier $namespaceContext
    );
}
