<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Parser;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericNameApplication;

/**
 * Interface GenericTypeNameParser
 * @package NicMart\Generics\Name\Generic\Parser
 */
interface GenericTypeNameParser
{
    /**
     * @param FullName $name
     * @return bool
     */
    public function isGeneric(FullName $name);

    /**
     * @param FullName $name
     * @return GenericNameApplication
     */
    public function parse(FullName $name);

    /**
     * @param GenericNameApplication $application
     * @return mixed
     */
    public function serialize(GenericNameApplication $application);
}