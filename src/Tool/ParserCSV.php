<?php
declare(strict_types=1);

namespace App\Tool;

class ParserCSV 
{
    private $filename;
    private $separator = '';
    
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function parse(int $headerSize): array
    {
        $file = fopen($this->filename, 'r');

        if ($file === false || $headerSize <= 0) {
            throw new \Exception('noneInterval');
        }

        $header = fgets($file);

        if (count(explode(';', $header)) === $headerSize) {
            $this->separator = ';';
        } elseif (count(explode(',', $header)) === $headerSize) {
            $this->separator = ',';
        }

        // if neither ; or , was ok as a separator
        if ($this->separator === '') {
            throw new \Exception('noneInterval');
        }

        $returnArray = [];
        while ($line = fgets($file)) {
            $lineArray = explode($this->separator, rtrim($line));
            $returnArray[] = array_map(function($element) { return htmlentities($element); }, $lineArray);
        }
        fclose($file);

        if (empty($returnArray)) {
            throw new \Exception('noneInterval');
        }

        return $returnArray;
    }
}
