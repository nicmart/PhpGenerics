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
     * @var Uses
     */
    private $uses;

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
     * @param Uses $uses
     */
    public function __construct(
        Namespace_ $namespace,
        Uses $uses = null
    ) {
        $this->namespace = $namespace;
        $this->uses = $uses ?: new Uses(array());
    }

    /**
     * @return Namespace_
     */
    public function namespace_()
    {
        return $this->namespace;
    }

    /**
     * @return Uses
     */
    public function uses()
    {
        return $this->uses;
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
        $new->uses = $this->uses->withUse($use);

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
     * @param Name $name
     * @return FullName
     */
    public function qualify(Name $name)
    {
        if ($name instanceof FullName) {
            return $name;
        }

        if ($name->isRoot()) {
            return $name->toFullName();
        }

        $first = $name->first();

        if ($this->uses->hasUse($first)) {
            return $this->uses->getUse($first)->qualify($name);
        }

        return $this->namespace->qualify($name);
    }

    /**
     * @param FullName $fullName
     * @return Name
     */
    public function simplify(FullName $fullName)
    {
        $useRelativeName = $this->uses()->simplify($fullName);
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