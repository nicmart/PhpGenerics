<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Evaluation;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;

class Psr0SourceUnitEvaluation implements SourceUnitEvaluation
{
    /**
     * @var
     */
    private $folder;

    /**
     * Psr0SourceUnitEvaluation constructor.
     * @param string $folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    public function evaluate(SourceUnit $sourceUnit)
    {
        $name = $sourceUnit->name();
        $filePath = $this->filePath($name);
        $this->createFolders($name);
        file_put_contents(
            $filePath,
            $sourceUnit->source()
        );
        include $filePath;
    }

    private function filePath(FullName $name)
    {
        return sprintf(
            "%s/%s.php",
            $this->folder,
            $name->toString("/")
        );
    }

    private function createFolders(FullName $name)
    {
        $path = $this->folder;

        if (!file_exists($this->folder)) {
            mkdir($this->folder);
        }

        $parts = array_slice($name->parts(), 0, -1);

        foreach ($parts as $part) {
            $path .= "/" . $part;
            if (!file_exists($path)) {
                mkdir($path);
            }
        }
    }
}