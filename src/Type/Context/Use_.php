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
     * Use_ constructor.
     * @param $name
     * @param null $alias
     */
    public function __construct($name, $alias = null)
    {
        // Must be always a global path
        $this->name = ltrim($name, "\\");
        $parts = explode("\\", $name);
        $this->alias = $alias ?: $parts[count($parts) - 1];
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function nameParts()
    {
        return explode("\\", $this->name);
    }

    /**
     * @return string
     */
    public function alias()
    {
        return $this->alias;
    }
}