<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;

/**
 * Class GenericNameApplication
 * @package NicMart\Generics\Name\Generic
 */
final class GenericNameApplication
{
    /**
     * @var FullName
     */
    private $main;

    /**
     * @var Name[]
     */
    private $arguments = array();

    /**
     * GenericNameApplication constructor.
     * @param FullName $main
     * @param Name[] $arguments
     */
    public function __construct(FullName $main, array $arguments = array())
    {
        $this->main = $main;

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    /**
     * @return FullName
     */
    public function main()
    {
        return $this->main;
    }

    /**
     * @return array
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * @param Name $argument
     */
    private function addArgument(Name $argument)
    {
        $this->arguments[] = $argument;
    }
}