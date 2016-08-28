<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP7\Func;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;
use NicMart\Generics\Variable\T3;

use NicMart\Generics\Example\PHP7\Func\Function1«T1·T2»;
use NicMart\Generics\Example\PHP7\Func\Function1«T1·T3»;
use NicMart\Generics\Example\PHP7\Func\Function1«T2·T3»;

class Composition«T1·T2·T3» implements Function1«T1·T3», Generic
{
    /**
     * @var \NicMart\Generics\Example\PHP7\Func\Function1«T2·T3»
     */
    private $f;
    /**
     * @var \NicMart\Generics\Example\PHP7\Func\Function1«T1·T2»
     */
    private $g;

    public function __construct(
        Function1«T2·T3» $f,
        Function1«T1·T2» $g
    ) {
        $this->f = $f;
        $this->g = $g;
    }

    public function __invoke(T1 $x): T3
    {
        return call_user_func($this->f, call_user_func($this->g, $x));
    }

}