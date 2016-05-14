<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Composer;

/**
 * Interface DirectoryResolver
 * 
 * Given a name, determine the possible folders that can contain a
 * class/interface/trait with that name
 * 
 * @package NicMart\Generics\Composer
 */
interface DirectoryResolver
{
    /**
     * @param $name
     * @return mixed
     */
    public function directories($name);
}