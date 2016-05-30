<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;


use PhpParser\Node\Name;

class GenericName extends Name
{
    /** @var Name[] */
    public $typeArguments;

    public function __construct($parts, array $typeArguments, array $attributes)
    {
        parent::__construct($parts, $attributes);
        $this->typeArguments = $typeArguments;
    }

    public function arity()
    {
        return count($this->typeArguments);
    }

    public function getSubNodeNames()
    {
        return array_merge(
            parent::getSubNodeNames(),
            array("typeArguments")
        );
    }

}