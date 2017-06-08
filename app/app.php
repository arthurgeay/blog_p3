<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => function () use ($app) {
                return new blog_p3\DAO\UserDAO($app['db']);
            },
        ),
    ),
));

// Register services
$app['dao.article'] = function ($app) {
    return new blog_p3\DAO\ArticleDAO($app['db']);
};
$app['dao.comment'] = function ($app)
{
	$commentDAO = new blog_p3\DAO\CommentDAO($app['db']);
	$commentDAO->setArticleDAO($app['dao.article']);
	return $commentDAO;
};
$app['dao.user'] = function ($app)
{
	return new blog_p3\DAO\UserDAO($app['db']);
};