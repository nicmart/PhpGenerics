<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\Source;


/**
 * Class CallerFilenameResolver
 * @package NicMart\Generics\Infrastructure\Source
 */
class CallerFilenameResolver
{
    /**
     * @return mixed
     */
    public function filename()
    {
        $trace = debug_backtrace();

        foreach ($trace as $entry) {
            if (isset($entry["file"]) && $entry["file"] != __FILE__) {
                return $entry["file"];
            }
        }

        throw new \UnderflowException("Found no filename in the backtrace");
    }
}