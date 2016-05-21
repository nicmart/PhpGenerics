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

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Generic\GenericNameResolver;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class GenericNameResolver
 * @package NicMart\Generics\Composer
 */
class ComposerGenericNameResolver implements GenericNameResolver
{
    /**
     * @var DirectoryResolver
     */
    private $directoryResolver;

    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * GenericNameResolver constructor.
     * @param GenericNameFactory $genericNameFactory
     * @param DirectoryResolver $directoryResolver
     */
    public function __construct(
        GenericNameFactory $genericNameFactory,
        DirectoryResolver $directoryResolver
    ) {
        $this->directoryResolver = $directoryResolver;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param GenericName $appliedGenericName
     * @return FullName
     */
    public function resolve(GenericName $appliedGenericName)
    {
        $mainName = $appliedGenericName->main();

        $nsName = $mainName->up();

        $dirs = $this->directoryResolver->directories($nsName->toString());
        
        $finder = new Finder();

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $finder->in($dir);
            }
        }

        $finder->name($this->regexp($appliedGenericName));

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $name = $nsName->down($file->getBasename(".php"));
            if ($this->isGeneric($name)) {
                return $name;
            }
        }

        throw new \UnderflowException(sprintf(
            "Unable to resolve GenericName file for applied generic %s",
            $appliedGenericName->main()->toString()
        ));
    }

    /**
     * @param FullName $name
     * @return mixed
     */
    private function isGeneric(FullName $name)
    {
        return is_a($name->toString(), '\NicMart\Generics\Generic', true);
    }

    /**
     * @param GenericName $appliedGenericName
     * @return mixed
     */
    private function regexp(GenericName $appliedGenericName)
    {
        $paramRegexpName = FullName::fromString(".+");
        $params = array_fill(0, $appliedGenericName->arity(), $paramRegexpName);
        $genericNameRegexp = $appliedGenericName->apply($params);
        $genericRegexpFullName = $this->genericNameFactory->fromGeneric($genericNameRegexp);

        return sprintf(
            "/^%s\.php$/",
            $genericRegexpFullName->last()->toString()
        );
    }
}