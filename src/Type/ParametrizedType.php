<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Name\FullName;

/**
 * Class ParametrizedType
 * @package NicMart\Generics\Type
 */
final class ParametrizedType implements ReferenceType
{
    /**
     * @var Type[]
     */
    private $arguments;

    /**
     * @var FullName
     */
    private $name;

    /**
     * GenericType constructor.
     * @param FullName $name
     * @param Type[] $arguments
     */
    public function __construct(FullName $name, array $arguments)
    {
        $this->name = $name;
        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return Type[]
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * @return int
     */
    public function arity()
    {
        return count($this->arguments);
    }

    /**
     * @param Type $argument
     */
    private function addArgument(Type $argument)
    {
        $this->arguments[] = $argument;
    }
}