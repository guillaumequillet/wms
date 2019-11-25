<?php
declare(strict_types=1);

namespace App\Tool;

class ParserCSV 
{
    private $filename;
    
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function parse(int $headerSize): ?array
    {
        $file = fopen($this->filename, 'r');

        if ($file === false) {
            return null;
        }

        $header = fgets($file);
        $separator = '';

        if (count(explode(';', $header)) === $headerSize) {
            $separator = ';';
        } elseif (count(explode(',', $header)) === $headerSize) {
            $separator = ',';
        }

        // if neither ; or , was ok as a separator
        if ($separator === '') {
            return null;
        }

        $returnArray = [];
        while ($line = fgets($file)) {
            $returnArray[] = explode($separator, rtrim($line));
        }
        fclose($file);

        if (empty($returnArray)) {
            return null;
        }

        return $returnArray;
    }
}
