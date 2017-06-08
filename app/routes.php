<?php

use Symfony\Component\HttpFoundation\Request;
use blog_p3\Domain\Comment;
use blog_p3\Form\Type\CommentType;

// Home page
$app->get('/', function () use ($app) {
	$articles = $app['dao.article']->findAll();
    return $app['twig']->render('index.html.twig', array('articles' => $articles));
})->bind('home');

// Blog page
$app->get('/blog', function () use ($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('blog.html.twig', array('articles' => $articles));
})->bind('blog');

// Article details with comments
$app->match('/article/{id}', function ($id, Request $request) use ($app) {
	$articles = $app['dao.article']->findAll();
    $article = $app['dao.article']->find($id);

    $commentFormView = null;
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
    return $app['twig']->render('article.html.twig', array('articles' => $articles, 'article' => $article, 'comments' => $comments, 'commentForm' => $commentFormView));
})->bind('article');


// Login form
$app->get('/login', function(Request $request) use ($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
        'articles' => $articles
    ));
})->bind('login');

