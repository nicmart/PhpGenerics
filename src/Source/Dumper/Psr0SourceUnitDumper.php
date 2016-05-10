<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 13:39
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Source\Dumper;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;

class Psr0SourceUnitDumper implements SourceUnitDumper
{
    /**
     * @var
     */
    private $folder;

    /**
     * @param string $folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param SourceUnit $sourceUnit
     * @return DumpResult
     */
    public function dump(SourceUnit $sourceUnit)
    {
        $name = $sourceUnit->name();
        $filePath = $this->filePath($name);
        $this->createFolders($name);
        file_put_contents(
            $filePath,
            "<?php\n\n" . $sourceUnit->source()
        );

        return new DumpResult(
            $filePath,
            $sourceUnit
        );
    }

    /**
     * @param FullName $name
     * @return string
     */
    private function filePath(FullName $name)
    {
        return sprintf(
            "%s/%s.php",
            $this->folder,
            $name->toString("/")
        );
    }

    /**
     * @param FullName $name
     */
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