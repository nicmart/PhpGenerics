<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer\Name;

use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Transformer\NameTransformer;
use PhpParser\Node\Name;

/**
 * Class NameAdapterPhpNameTransformer
 *
 * Lift a name transformer to a php name transformer
 *
 * @package NicMart\Generics\AST\Transformer\Name
 */
class NameAdapterPhpNameTransformer implements PhpNameTransformer
{
    /**
     * @var PhpNameAdapter
     */
    private $adapter;

    /**
     * @var NameTransformer
     */
    private $nameTransformer;

    /**
     * NameAdapterPhpNameTransformer constructor.
     * @param NameTransformer $nameTransformer
     * @param PhpNameAdapter $adapter
     */
    public function __construct(NameTransformer $nameTransformer, PhpNameAdapter $adapter)
    {
        $this->adapter = $adapter;
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Name $phpName
     * @return Name
     */
    public function __invoke(Name $phpName)
    {
        return $this->adapter->toPhpName(
            $this->nameTransformer->__invoke(
                $this->adapter->fromPhpName($phpName)
            )
        );
    }
}