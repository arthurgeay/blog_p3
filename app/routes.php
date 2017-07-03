<?php

use Symfony\Component\HttpFoundation\Request;
use blog_p3\Domain\Comment;
use blog_p3\Domain\Article;
use blog_p3\Form\Type\CommentType;
use blog_p3\Form\Type\ArticleType;

// Home page
$app->get('/', function () use ($app) {
	$articles = $app['dao.article']->findAll();
    //For the footer
    $latestArticles = $app['dao.article']->findLatestArticles();

    return $app['twig']->render('index.html.twig', array('articles' => $articles, 'latestArticles' => $latestArticles));
})->bind('home');

// Blog page
$app->get('/blog', function () use ($app) {
    $articles = $app['dao.article']->findAll();
    //For the footer
    $latestArticles = $app['dao.article']->findLatestArticles();
    return $app['twig']->render('blog.html.twig', array('articles' => $articles, 'latestArticles' => $latestArticles));
})->bind('blog');

// Article details with comments
$app->match('/article/{id}', function ($id, Request $request) use ($app) {
	//For the footer
    $latestArticles = $app['dao.article']->findLatestArticles();

    $article = $app['dao.article']->find($id);

    
    $comment = new Comment();
    $comment->setArticle($article);
    $commentForm = $app['form.factory']->create(CommentType::class, $comment);
    $commentForm->handleRequest($request);


    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Votre commentaire a bien été ajouté.');
        }
        $commentFormView = $commentForm->createView();

    $comments = $app['dao.comment']->findAllByArticle($id);
    return $app['twig']->render('article.html.twig', array('article' => $article, 'comments' => $comments, 'commentForm' => $commentFormView, 'latestArticles' => $latestArticles));
})->bind('article');


// Login form
$app->get('/login', function(Request $request) use ($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('/admin/login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
        'articles' => $articles
    ));
})->bind('login');




// Admin Home page
$app->get('/admin', function() use ($app) {
    $articles = $app['dao.article']->findAll();
    $comments = $app['dao.comment']->findAll();
    $comsReports = $app['dao.comment']->findComReport();

    return $app['twig']->render('/admin/admin.html.twig', array(
        'articles' => $articles,
        'comments' => $comments,
        'comsReports' => $comsReports
        ));
})->bind('admin');




// Add a new article
$app->match('/admin/article/add', function(Request $request) use ($app) {
    $articles = $app['dao.article']->findAll();

    $article = new Article();
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'L\'article a bien été créé.');
    }
    return $app['twig']->render('/admin/article_form.html.twig', array(
        'articles' => $articles,
        'title' => 'Nouvel article',
        'articleForm' => $articleForm->createView()));
})->bind('admin_article_add');




// Edit an existing article
$app->match('/admin/article/{id}/edit', function($id, Request $request) use ($app) {
    $articles = $app['dao.article']->findAll();

    $article = $app['dao.article']->find($id);
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'L\'article a bien été mis à jour.');
    }
    return $app['twig']->render('/admin/article_form.html.twig', array(
        'articles' => $articles, 
        'title' => 'Modifier l\'article',
        'articleForm' => $articleForm->createView()));
})->bind('admin_article_edit');



// Remove an article
$app->get('/admin/article/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all associated comments
    $app['dao.comment']->deleteAllByArticle($id);
    // Delete the article
    $app['dao.article']->delete($id);
    $app['session']->getFlashBag()->add('success', 'L\'article a bien été supprimé.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_article_delete');



// Remove a comment
$app->get('/admin/comment/{id}/delete', function($id, Request $request) use ($app) {
    $app['dao.comment']->delete($id);
    $app['session']->getFlashBag()->add('success', 'Le commentaire a bien été supprimé.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_comment_delete');



//Report a comment
$app->get('article/comment/{id}/report', function($id, Request $request) use ($app)
{  
    $comId = $app['dao.comment']->find($id);

    $report = $app['dao.comment']->reportCom($comId);

    $app['session']->getFlashBag()->add('success-report', 'Le commentaire a bien été signalé.');

    $articleId = $app['dao.article']->find($id);

    return $app->redirect($app['url_generator']->generate('article', array('id' => $articleId)));

})->bind('report_comment');






