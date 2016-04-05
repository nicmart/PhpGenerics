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

/**
 * Class Use_
 * @package NicMart\Generics\Type\Php
 */
final class Use_
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var null
     */
    private $alias;

    /**
     * @var Path
     */
    private $path;

    /**
     * @param $pathString
     * @param $nameString
     * @return Use_
     */
    public static function fromStrings(
        $pathString,
        $nameString = null
    ) {
        return new self(
            Path::fromString($pathString),
            isset($nameString) ? new SimpleName($nameString) : null
        );
    }

    /**
     * Use_ constructor.
     * @param Path $path
     * @param SimpleName $alias
     */
    public function __construct(Path $path, SimpleName $alias = null)
    {
        $this->alias = $alias ?:  new SimpleName($path->name());
        $this->path = $path;
    }

    /**
     * @return Path
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @return SimpleName
     */
    public function alias()
    {
        return $this->alias;
    }
}