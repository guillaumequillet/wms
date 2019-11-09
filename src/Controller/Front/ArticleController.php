<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Entity\Article;
use App\Model\Manager\ArticleManager;

class ArticleController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = new ArticleManager();
    }

    public function show(?int $id): void
    {
        // we display all the articles if $id is null
        if (is_null($id)) {
            $template = 'article/list.twig.html';
            $articles = [];
            $res = $this->entityManager->findAllArticles();

            if (!is_null($res)) {
                foreach($res as $article) {
                    $articles[] = (new Article())->hydrate($article);                
                }
            }

            $data = [
                'articles' => $articles
            ];
        }

        // if some article id was specified, we only show this one
        if (!is_null($id))
        {
            $template = 'article/single.twig.html';
            $res = $this->entityManager->findArticleWithId($id);
            $article = null;

            if (!is_null($res)) {
                $article = (new Article())->hydrate($res);
            }

            $data = [
                'article' => $article
            ];
        }

        if (!is_null($template) && !is_null($data)) {
            $this->getView()->render($template, $data);
        }
    }
}
