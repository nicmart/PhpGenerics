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
        return new self(new Namespace_($namespace));
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
     * @param string $alias
     * @return bool
     */
    public function hasUse($alias)
    {
        return isset($this->usesByAliases[$alias]);
    }

    /**
     * @param string $alias
     * @return Use_
     * @throws UnderflowException
     */
    public function getUse($alias)
    {
        if (!$this->hasUse($alias)) {
            throw new UnderflowException("Undefined use statement for alias $alias");
        }

        return $this->usesByAliases[$alias];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasUseByName($name)
    {
        return isset($this->usesByName[$name]);
    }

    /**
     * @param string $name
     * @return Use_
     * @throws UnderflowException
     */
    public function getUseByName($name)
    {
        if (!$this->hasUseByName($name)) {
            throw new UnderflowException("Undefined use statement for name $name");
        }

        return $this->usesByName[$name];
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
        $this->usesByAliases[$use->alias()] = $use;
        $this->usesByName[$use->name()] = $use;
    }
}