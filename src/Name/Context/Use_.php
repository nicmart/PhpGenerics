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

/**
 * Class Use_
 * @package NicMart\Generics\Name\Php
 */
final class Use_ implements NameSimplifier, NameQualifier
{
    /**
     * @var null
     */
    private $alias;

    /**
     * @var FullName
     */
    private $name;

    /**
     * @param string $name
     * @param string $alias
     * @return Use_
     */
    public static function fromStrings(
        $name,
        $alias = null
    ) {
        return new self(
            FullName::fromString($name),
            isset($alias) ? new SimpleName($alias) : null
        );
    }

    /**
     * Use_ constructor
     *
     * @param FullName $name
     * @param SimpleName $alias
     */
    public function __construct(FullName $name, SimpleName $alias = null)
    {
        $this->alias = $alias ?: $name->last();
        $this->name = $name;
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return SimpleName
     */
    public function alias()
    {
        return $this->alias;
    }

    /**
     * @param RelativeName $relativeName
     * @return FullName
     */
    public function qualify(RelativeName $relativeName)
    {
        if (!$relativeName->length()) {
            return $relativeName->toFullName();
        }

        $first = $relativeName->first();

        if ($first != $this->alias) {
            return $relativeName->toFullName();
        }

        return $this->name()->append($relativeName->tail());
    }

    /**
     * @param FullName $fullName
     * @return RelativeName
     */
    public function simplify(FullName $fullName)
    {
        if (!$this->name->isPrefixOf($fullName)) {
            return $fullName->toRelative();
        }

        return $this->alias->toRelativeName()->append(
            $fullName->from($this->name)
        );
    }
}