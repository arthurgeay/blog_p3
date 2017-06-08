<?php

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

$app->get('/article/{id}', function ($id) use ($app) {
	$articles = $app['dao.article']->findAll();
    $article = $app['dao.article']->find($id);
    $comments = $app['dao.comment']->findAllByArticle($id);
    return $app['twig']->render('article.html.twig', array('articles' => $articles, 'article' => $article, 'comments' => $comments));
})->bind('article');