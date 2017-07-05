<?php

namespace blog_p3\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swift_SmtpTransport;
use Swift_Mailer;
use blog_p3\Domain\Comment;
use blog_p3\Domain\Article;
use blog_p3\Domain\Newsletter;
use blog_p3\Domain\Mail;
use blog_p3\Domain\Contact;
use blog_p3\Form\Type\NewsletterType;
use blog_p3\Form\Type\CommentType;
use blog_p3\Form\Type\ContactType;


class HomeController
{
	/**
	 * Home page controller
	 * 
	 * @param  Application $app Silex Application
	 * @param  Request $request Incoming request
	 */
	public function indexAction(Request $request, Application $app)
	{
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
	}

	/**
	 * Blog page Controller
	 * 
	 * @param  Request $request Incoming request
	 * @param  Application $app Silex Application
	 */
	public function blogAction(Request $request, Application $app)
	{
	    $articles = $app['dao.article']->findAll();

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

	    return $app['twig']->render('blog.html.twig', array('articles' => $articles, 'latestArticles' => $latestArticles, 'newsletterForm' => $newsletterFormView));
	}



	/**
	 * Article page controller
	 * 
	 * @param $id The article id
	 * @param  Request $request Incoming request
	 * @param  Application $app Silex Application 
	 */
	public function articleAction($id, Request $request, Application $app)
	{
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
	}

	/**
	 * Report comment Controller
	 * 
	 * @param $id Id of comment
	 * @param Request $request Incoming request
	 * @param Application $app Silex Application
	 */
	public function reportCommentAction($id, Request $request, Application $app)
	{	  
	    $report = $app['dao.comment']->reportCom($id);
	    $comment = $app['dao.comment']->find($id);

	    $app['session']->getFlashBag()->add('success-report', 'Le commentaire a bien été signalé.');

	    

	    return $app->redirect($app['url_generator']->generate('article', array('id' => $comment->getArticle()->getId())));
	}

	/**
	 * Login page Controller
	 * 
	 * @param Request $request Incoming request
	 * @param Application $app Silex Application
	 */
	public function loginAction(Request $request, Application $app)
	{
	    $articles = $app['dao.article']->findAll();
	    return $app['twig']->render('/admin/login.html.twig', array(
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username'),
	        'articles' => $articles
	    ));
	}

	
	/**
	 * Contact page controller
	 * 
	 * @param Request $request Incoming request
	 * @param Application $app Silex Application
	 */
	public function contactAction(Request $request, Application $app)
	{
	    $contact = new Contact();
	    $contactForm = $app['form.factory']->create(ContactType::class, $contact);
	    $contactForm->handleRequest($request);


	    if ($contactForm->isSubmitted() && $contactForm->isValid()) {
	            $app['dao.contact']->save($contact);

	            $mailContent = $app['dao.contact']->find();

	            // Create the Transport
	            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
	            ->setUsername('arthurgeay.contact@gmail.com')
	            ->setPassword('lwhvjswfjrvutmnd');

	            // Create the Mailer 
	            $mailer = new Swift_Mailer($transport);

	            $body = 'Ce message a été envoyé par ' . $mailContent->getMail() .'<br /> ' . '<p><strong>Contenu du message : </strong></p><p>' .$mailContent->getContent() . '</p>';

	            $message = \Swift_Message::newInstance()
	                ->setSubject($mailContent->getTitle() . ' - Contact Jean Forteroche')
	                ->setFrom($mailContent->getMail())
	                ->setTo(array('arthur.geay@sfr.fr'))
	                ->setBody($body, 'text/html');

	            // Send the message
	            $result = $mailer->send($message);

	            if($result)
	            {
	                $app['dao.contact']->delete($mailContent->getId());
	                $app['session']->getFlashBag()->add('success', 'Votre message a bien été envoyé.');
	            }
	        }

	        $contactFormView = $contactForm->createView();

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

	    return $app['twig']->render('contact.html.twig', array('contactForm' => $contactFormView, 'title' => 'Contact', 'latestArticles' => $latestArticles, 'newsletterForm' => $newsletterFormView));
	}
}