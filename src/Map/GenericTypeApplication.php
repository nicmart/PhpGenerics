<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Map;


use NicMart\Generics\Name\FullName;

/**
 * Class GenericTypeApplication
 * @package NicMart\Generics\Map
 */
final class GenericTypeApplication
{
    /**
     * @var FullName[]
     */
    private $typeParameters = array();
    /**
     * @var FullName
     */
    private $generic;

    /**
     * GenericTypeApplication constructor.
     * @param FullName $generic
     * @param FullName[] $params
     */
    public function __construct(
        FullName $generic,
        array $params
    ) {
        foreach ($params as $param) {
            $this->addParam($param);
        }

        $this->generic = $generic;
    }

    /**
     * @return FullName
     */
    public function generic()
    {
        return $this->generic;
    }

    /**
     * @return FullName[]
     */
    public function parameters()
    {
        return $this->typeParameters;
    }

    /**
     * @param FullName $typeParam
     */
    private function addParam(FullName $typeParam)
    {
        $this->typeParameters[] = $typeParam;
    }
}