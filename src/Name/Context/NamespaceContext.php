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
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\SimpleName;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Name\Transformer\NameSimplifier;
use UnderflowException;

/**
 * Class NamespaceContext
 *
 * @package NicMart\Generics\Name\Context
 */
final class NamespaceContext implements NameSimplifier, NameQualifier
{
    /**
     * @var Namespace_
     */
    private $namespace;

    /**
     * @var Use_[]
     */
    private $usesByAliases = array();

    /**
     * @var Use_[]
     */
    private $usesByName = array();

    /**
     * @return NamespaceContext
     */
    public static function emptyContext()
    {
        return new self(Namespace_::globalNamespace());
    }

    /**
     * @param array $parts
     * @return NamespaceContext
     */
    public static function fromNamespaceParts(array $parts)
    {
        return new self(Namespace_::fromParts($parts));
    }

    /**
     * @param string $namespace
     * @return NamespaceContext
     */
    public static function fromNamespaceName($namespace)
    {
        return new self(Namespace_::fromString($namespace));
    }

    /**
     * NamespaceContext constructor.
     * @param Namespace_ $namespace
     * @param Use_[] $uses
     */
    public function __construct(
        Namespace_ $namespace,
        array $uses = array()
    ) {
        $this->namespace = $namespace;

        foreach ($uses as $use) {
            $this->addUse($use);
        }
    }

    /**
     * @return Namespace_
     */
    public function getNamespace()
    {
        return $this->namespace;
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
     * @return NamespaceContext
     */
    public function withUse(Use_ $use)
    {
        $new = clone $this;

        $new->addUse($use);

        return $new;
    }

    /**
     * @param Namespace_ $namespace
     * @return NamespaceContext
     */
    public function withNamespace(Namespace_ $namespace)
    {
        $new = clone $this;

        $new->namespace = $namespace;

        return $new;
    }

    /**
     * @param string $nameString
     * @return FullName
     * @throws \UnderflowException
     */
    public function qualifyFromString($nameString)
    {
        if (substr($nameString, 0, 1) == "\\") {
            return FullName::fromString($nameString);
        }

        return $this->qualify(
            RelativeName::fromString($nameString)
        );
    }

    /**
     * @param RelativeName $relativeName
     * @return FullName
     * @throws \UnderflowException
     */
    public function qualify(RelativeName $relativeName)
    {
        if ($relativeName->isRoot()) {
            return $relativeName->toFullName();
        }

        $first = $relativeName->first();

        if ($this->hasUse($first)) {
            return $this->getUse($first)->qualify($relativeName);
        }

        return $this->namespace->qualify($relativeName);
    }

    /**
     * @param FullName $fullName
     * @return RelativeName
     */
    public function simplify(FullName $fullName)
    {
        $useRelativeName = $fullName->toRelative();

        for (
            $prefix = $fullName;
            !$prefix->isRoot();
            $prefix = $prefix->up()
        ) {
            if ($this->hasUseByName($prefix)) {
                $useRelativeName = $this->getUseByName($prefix)->simplify($fullName);
                break;
            }
        }

        $nsRelativeName = $this->namespace->simplify($fullName);

        return $nsRelativeName->length() <= $useRelativeName->length()
            ? $nsRelativeName
            : $useRelativeName
        ;
    }

    /**
     * @param Use_ $use
     */
    private function addUse(Use_ $use)
    {
        $this->usesByAliases[$use->alias()->toString()] = $use;
        $this->usesByName[$use->name()->toString()] = $use;
    }
}