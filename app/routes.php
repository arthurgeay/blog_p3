<?php

// Home page
$app->get('/', function () use ($app) {
	$articles = $app['dao.article']->findAll();
    return $app['twig']->render('index.html.twig', array('articles' => $articles));
});

// Blog page
$app->get('/blog', function () use ($app) {
    $articles = $app['dao.article']->findAll();
    return $app['twig']->render('blog.html.twig', array('articles' => $articles));
});