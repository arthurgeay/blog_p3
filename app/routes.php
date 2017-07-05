<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use blog_p3\Domain\Comment;
use blog_p3\Domain\Article;
use blog_p3\Domain\Newsletter;
use blog_p3\Domain\Mail;
use blog_p3\Form\Type\CommentType;
use blog_p3\Form\Type\ArticleType;
use blog_p3\Form\Type\NewsletterType;
use blog_p3\Form\Type\MailType;

// Home page
$app->match('/', function (Request $request) use ($app) {
	$articles = $app['dao.article']->findAll();

    $counter = $app['dao.article']->counterSlide();

    //For the footer and carousel
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

    return $app['twig']->render('index.html.twig', array('articles' => $articles, 'latestArticles' => $latestArticles, 'counter' => $counter, 'newsletterForm' => $newsletterFormView));
})->bind('home');

// Blog page
$app->match('/blog', function (Request $request) use ($app) {
    $articles = $app['dao.article']->findAll();

    $nbOfComments = $app['dao.comment']->count();

    //For the footer
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

    return $app['twig']->render('blog.html.twig', array('articles' => $articles, 'latestArticles' => $latestArticles, 'nbOfComments' => $nbOfComments, 'newsletterForm' => $newsletterFormView));
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


    //Newsletter form 
    $newsletter = new Newsletter();
    $newsletterForm = $app['form.factory']->create(NewsletterType::class, $newsletter);
    $newsletterForm->handleRequest($request);


    if ($newsletterForm->isSubmitted() && $newsletterForm->isValid()) {
            $app['dao.newsletter']->save($newsletter);
            $app['session']->getFlashBag()->add('success', 'Vous êtes bien inscrit à la newsletter.');
        }

    $newsletterFormView = $newsletterForm->createView();

    return $app['twig']->render('article.html.twig', array('article' => $article, 'comments' => $comments, 'commentForm' => $commentFormView, 'latestArticles' => $latestArticles, 'newsletterForm' => $newsletterFormView));
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

    $subscribers = $app['dao.newsletter']->count();
    $allSubscribers = $app['dao.newsletter']->findAll();

    return $app['twig']->render('/admin/admin.html.twig', array(
        'articles' => $articles,
        'comments' => $comments,
        'comsReports' => $comsReports,
        'subscribers' => $subscribers,
        'allSubscribers' => $allSubscribers
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
    $report = $app['dao.comment']->reportCom($id);
    $comment = $app['dao.comment']->find($id);

    $app['session']->getFlashBag()->add('success-report', 'Le commentaire a bien été signalé.');

    

    return $app->redirect($app['url_generator']->generate('article', array('id' => $comment->getArticle()->getId())));

})->bind('report_comment');

//Send a newsletter
$app->match('/admin/newsletter', function(Request $request) use ($app)
{
    //Mail form 
    $mail = new Mail();
    $mailForm = $app['form.factory']->create(MailType::class, $mail);
    $mailForm->handleRequest($request);


    if ($mailForm->isSubmitted() && $mailForm->isValid()) {
            $app['dao.mail']->save($mail);
            
            $mailContent = $app['dao.mail']->find();

            $subscribers = $app['dao.newsletter']->allMail();

            // Create the Transport
            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('arthurgeay.contact@gmail.com')
            ->setPassword('lwhvjswfjrvutmnd');

            // Create the Mailer 
            $mailer = new Swift_Mailer($transport);

            $body = $mailContent->getContent();

            $message = \Swift_Message::newInstance()
                ->setSubject($mailContent->getTitle())
                ->setFrom(array('arthurgeay.contact@gmail.com' => 'Jean Forteroche'))
                ->setBcc($subscribers)
                ->setBody($body, 'text/html');

            // Send the message
            $result = $mailer->send($message);

            if($result)
            {
             $app['session']->getFlashBag()->add('success', 'La newsletter a bien été envoyé');   
            }
            
        }

    $mailFormView = $mailForm->createView();

    return $app['twig']->render('/admin/mail_form.html.twig', array('mailForm' => $mailFormView, 'title' => 'Rédiger une newsletter'));

})->bind('admin_newsletter_add');

// Remove a comment
$app->get('/admin/newsletter/{id}/delete', function($id, Request $request) use ($app) {
    $app['dao.newsletter']->delete($id);
    $app['session']->getFlashBag()->add('success', 'L\'inscrit a bien été supprimé.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_subscriber_delete');







