<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\Front\ArticleController;

function myLog(string $message): void
{
    echo "<script>console.log(\"" . $message . "\");</script>";
}


myLog("essai pour voir");
(new ArticleController)->show(null);