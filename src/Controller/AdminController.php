<?php

namespace blog_p3\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Swift_SmtpTransport;
use Swift_Mailer;
use blog_p3\Domain\Article;
use blog_p3\Domain\User;
use blog_p3\Domain\Mail;
use blog_p3\Form\Type\ArticleType;
use blog_p3\Form\Type\CommentType;
use blog_p3\Form\Type\NewsletterType;
use blog_p3\Form\Type\MailType;

class AdminController 
{

    /**
     * Admin home page controller
     * 
     * @param Application $app Silex Application
     */
    public function adminAction(Application $app)
    {
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
    }




    /**
     * Adding article page Controller
     * 
     * @param Request $request Incoming request
     * @param Application $app Silex Application
     */
    public function addArticleAction(Request $request, Application $app)
    {
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
    }




    /**
     * Editing article page Controller
     * 
     * @param  $id Id of article
     * @param Request $request Incoming request
     * @param Application $app Silex Application
     */
    public function editArticleAction($id, Request $request, Application $app)
    {
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
    }



    /**
     * Remove article page Controller
     * 
     * @param  $id Id of article
     * @param Request $request Incoming request
     * @param Application $app Silex Application
     */
    public function removeArticleAction($id, Request $request, Application $app)
    {
        // Delete all associated comments
        $app['dao.comment']->deleteAllByArticle($id);
        // Delete the article
        $app['dao.article']->delete($id);
        $app['session']->getFlashBag()->add('success', 'L\'article a bien été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }



    /**
     * Remove comment page Controller
     * 
     * @param  $id Id of comment
     * @param Request $request Incoming request
     * @param Application $app Silex Application
     */
    public function removeCommentAction($id, Request $request, Application $app)
    {
        $app['dao.comment']->delete($id);
        $app['session']->getFlashBag()->add('success', 'Le commentaire a bien été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }



    /**
     * Sending Newsletter page controller
     * 
     * @param Request $request Incoming request
     * @param  Application $app Silex Application
     */
    public function sendNewsletterAction(Request $request, Application $app)
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
    }

    /**
     * Removing subscriber page controller
     * 
     * @param $id Id of subscriber
     * @param Request $request Incoming request
     * @param Application $app Silex Applicaiton
     */
    public function removeSubscriberAction($id, Request $request, Application $app)
    {
        $app['dao.newsletter']->delete($id);
        $app['session']->getFlashBag()->add('success', 'L\'inscrit a bien été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
}