<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Entity\Article;
use App\Model\Manager\ArticleManager;
use App\Tool\Token;

use League\Csv\Reader;

class ArticleController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = new ArticleManager();
        $this->token = new Token();
    }

    public function delete(int $id): void
    {
        $this->entityManager->deleteArticle($id);
        header('location: index.php?page=article&action=show');
    }

    public function import(): void
    {
        myLog(var_dump($_FILES));
    }

    public function showlist(?int $param): void 
    {
        $template = 'article/list.twig.html';
        $articles = [];
        $res = $this->entityManager->findAllArticles();

        if (!is_null($res)) {
            foreach($res as $article) {
                $articles[] = (new Article())->hydrate($article);                
            }
        }

        $data = [
            'articles' => $articles,
        ];        

        if (!is_null($param)) {
            $data['param'] = $param;
        }

        if (!is_null($template) && !is_null($data)) {
            $data['token'] = $this->token->generateString();
            $this->getView()->render($template, $data);
        }
    }

    public function show(?int $id): void
    {
        $template = 'article/single.twig.html';
        $data = [];

        // if some article id was specified, we only show this one
        if (!is_null($id))
        {
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
            $data['token'] = $this->token->generateString();
            $this->getView()->render($template, $data);
        }
    }
}
