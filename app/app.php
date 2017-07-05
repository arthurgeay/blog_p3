<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use blog_p3\Domain\Newsletter;
use blog_p3\Form\Type\NewsletterType;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register error handler
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Accès refusé.';
            break;
        case 404:
            $message = 'La ressource demandée n\'a pas pu être trouvée.';
            break;
        default:
            $message = "Quelque chose s\'est mal passé !.";
    }
    $latestArticles = $app['dao.article']->findLatestArticles();

    //Newsletter form 
    $newsletter = new Newsletter();
    $newsletterForm = $app['form.factory']->create(NewsletterType::class, $newsletter);
    $newsletterForm->handleRequest($request);


    if ($newsletterForm->isSubmitted() && $newsletterForm->isValid()) {
            $app['dao.newsletter']->save($newsletter);
            $app['session']->getFlashBag()->add('success', 'Vous êtes bien inscrit à la newsletter.');
        }

    $newsletterFormView = $newsletterForm->createView();

    return $app['twig']->render('error.html.twig', array('message' => $message, 'latestArticles' => $latestArticles, 'newsletterForm' => $newsletterFormView));
});

// Register service providers
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app['twig'] = $app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
});
$app->register(new Silex\Provider\ValidatorServiceProvider());
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
    'security.access_rules' => array(
        array('^/admin', 'ROLE_ADMIN'),
    ),
));

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());

    
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/blog_p3.log',
    'monolog.name' => 'blog_p3',
    'monolog.level' => $app['monolog.level']
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
$app['dao.newsletter'] = function ($app)
{
    return new blog_p3\DAO\NewsletterDAO($app['db']);
};

$app['dao.mail'] = function ($app)
{
    return new blog_p3\DAO\MailDAO($app['db']);
};

$app['dao.contact'] = function ($app)
{
    return new blog_p3\DAO\ContactDAO($app['db']);
};
