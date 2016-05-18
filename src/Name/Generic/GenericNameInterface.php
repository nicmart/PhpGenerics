<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 20/04/2016, 13:13
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Generic;

use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Interface GenericName
 * @package NicMart\Generics\Name\GenericName
 */
interface GenericNameInterface
{
    /**
     * @return FullName
     */
    public function name();

    /**
     * @return FullName
     */
    public function mainName();

    /**
     * @return int
     */
    public function arity();

    /**
     * @param FullName[] $names
     * @return FullName
     */
    public function apply(array $names);

    /**
     * @param NameQualifier $qualifier
     * @return FullName[]
     */
    public function parameters(NameQualifier $qualifier);

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

    /**
     * @param FullName[] $names
     * @return SimpleNameAssignmentContext
     */
    public function simpleAssignments(
        array $names
    );
}
