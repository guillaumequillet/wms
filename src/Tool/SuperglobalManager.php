<?php
declare(strict_types=1);

namespace App\Tool;

class SuperglobalManager
{
    public function findVariable(string $arrayName, string $value): ?string
    {
        $array = $this->getArray($arrayName);
        if (is_null($array)) {
            return null;
        }
        return isset($array[$value]) ? htmlentities($array[$value]) : null;
    }
    public function setVariable(string $arrayName, string $key, string $value): bool
    {
        $array = $this->getArray($arrayName);
        if (!is_null($array)) {
            $array[$key] = $value;
            return true;
        }
        return false;
    }
    private function getArray(string $table): ?array 
    {
        switch ($table) {
            case 'session':
                $array = &$_SESSION;
                break;
            case 'get':
                $array =  &$_GET;
                break;
            case 'post':
                $array =  &$_POST;
                break;            
            default:
                $array =  null;
                break;
        }
        return $array;
    }
}
