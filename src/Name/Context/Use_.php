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
use NicMart\Generics\Name\Path;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\SimpleName;

/**
 * Class Use_
 * @package NicMart\Generics\Name\Php
 */
final class Use_
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
        $this->alias = $alias ?: $name->name();
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
    public function qualifyRelativeName(RelativeName $relativeName)
    {
        $relativePath = $relativeName->path();
        if (!$relativePath->length()) {
            return new FullName($relativePath);
        }

        $first = $relativeName->path()->first();

        if ($first != $this->alias->toString()) {
            return new FullName($relativePath);
        }

        return new FullName(
            $this->name()->path()->append(
                $relativePath->tail()
            )
        );
    }

    /**
     * @param FullName $fullName
     * @return RelativeName
     */
    public function simplifyFullName(FullName $fullName)
    {
        $aliasPath = $this->alias->toPath();
        $thisPath = $this->name->path();
        $fullNamePath = $fullName->path();

        if (!$thisPath->isPrefixOf($fullNamePath)) {
            return new RelativeName($fullNamePath);
        }

        return new RelativeName(
            $aliasPath->append(
                $fullNamePath->from($thisPath)
            )
        );
    }
}