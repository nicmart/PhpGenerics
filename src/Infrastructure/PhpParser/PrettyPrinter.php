<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;


use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class PrettyPrinter
 * @package NicMart\Generics\Infrastructure\PhpParser
 */
class PrettyPrinter extends Standard
{
    public function pStmt_Property(Stmt\Property $node)
    {
        return parent::pStmt_Property($node) . "\n";
    }

    public function pStmt_ClassMethod(Stmt\ClassMethod $node)
    {
        return parent::pStmt_ClassMethod($node). "\n";
    }

    public function pStmt_ClassConst(Stmt\ClassConst $node)
    {
        return parent::pStmt_ClassConst($node). "\n";
    }

}