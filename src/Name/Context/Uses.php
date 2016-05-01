<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Context;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\SimpleName;
use NicMart\Generics\Name\Transformer\NameSimplifier;
use UnderflowException;

/**
 * Class Uses
 * @package NicMart\Generics\Name\Context
 */
final class Uses implements NameSimplifier
{
    /**
     * @var Use_[]
     */
    private $usesByAliases = array();

    /**
     * @var Use_[]
     */
    private $usesByName = array();

    /**
     * NamespaceContext constructor.
     * @param Use_[] $uses
     */
    public function __construct(
        array $uses = array()
    ) {
        foreach ($uses as $use) {
            $this->addUse($use);
        }
    }

    /**
     * @param FullName $fullName
     * @return Name
     */
    public function simplify(FullName $fullName)
    {
        for (
            $prefix = $fullName;
            !$prefix->isRoot();
            $prefix = $prefix->up()
        ) {
            if ($this->hasUseByName($prefix)) {
                return $this->getUseByName($prefix)->simplify($fullName);
            }
        }

        return $fullName;
    }


    /**
     * @return Use_[]
     */
    public function getUsesByAliases()
    {
        return $this->usesByAliases;
    }

    /**
     * @param SimpleName $alias
     * @return bool
     */
    public function hasUse(SimpleName $alias)
    {
        return isset($this->usesByAliases[$alias->toString()]);
    }

    /**
     * @param SimpleName $alias
     * @return Use_
     * @throws UnderflowException
     */
    public function getUse(SimpleName $alias)
    {
        if (!$this->hasUse($alias)) {
            throw new UnderflowException("Undefined use statement for alias $alias");
        }

        return $this->usesByAliases[$alias->toString()];
    }

    /**
     * @param FullName $name
     * @return bool
     */
    public function hasUseByName(FullName $name)
    {
        return isset($this->usesByName[$name->toString()]);
    }

    /**
     * @param FullName $name
     * @return Use_
     */
    public function getUseByName(FullName $name)
    {
        if (!$this->hasUseByName($name)) {
            throw new UnderflowException("Undefined use statement for name {$name->toString()}");
        }

        return $this->usesByName[$name->toString()];
    }

    /**
     * @param Use_ $use
     */
    private function addUse(Use_ $use)
    {
        $this->usesByAliases[$use->alias()->toString()] = $use;
        $this->usesByName[$use->name()->toString()] = $use;
    }

    /**
     * @param Use_ $use
     * @return NamespaceContext
     */
    public function withUse(Use_ $use)
    {
        $new = clone $this;

        $new->addUse($use);

        return $new;
    }

    /**
     * @param Uses $uses
     * @return Uses
     */
    public function merge(Uses $uses)
    {
        return new Uses(array_merge(
            $this->getUsesByAliases(),
            $uses->getUsesByAliases()
        ));
    }
}