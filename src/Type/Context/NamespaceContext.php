<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Context;

use NicMart\Generics\Type\Path;
use NicMart\Generics\Type\SimpleName;
use UnderflowException;

/**
 * Class NamespaceContext
 *
 * @package NicMart\Generics\Type\Context
 */
final class NamespaceContext
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
    private $usesByPath = array();

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
        return isset($this->usesByAliases[$alias->name()]);
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

        return $this->usesByAliases[$alias->name()];
    }

    /**
     * @param Path $path
     * @return bool
     */
    public function hasUseByPath(Path $path)
    {
        return isset($this->usesByPath[$path->toString()]);
    }

    /**
     * @param Path $path
     * @return Use_
     * @throws UnderflowException
     */
    public function getUseByPath(Path $path)
    {
        if (!$this->hasUseByPath($path)) {
            throw new UnderflowException("Undefined use statement for path {$path->toString()}");
        }

        return $this->usesByPath[$path->toString()];
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
     * @param Use_ $use
     */
    private function addUse(Use_ $use)
    {
        $this->usesByAliases[$use->alias()->name()] = $use;
        $this->usesByPath[$use->path()->toString()] = $use;
    }
}