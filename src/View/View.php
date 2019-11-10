<?php
declare(strict_types=1);

namespace App\View;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    private $twigLoader;
    private $twigEnvironment; 

    public function __construct()
    {
        $this->twigLoader = new FilesystemLoader('../templates');
        $this->twigEnvironment = new Environment($this->twigLoader, []); // we'll add cache later
    }

    public function render(string $template, array $data): void 
    {
        echo $this->twigEnvironment->render($template, $data);
    }
}
