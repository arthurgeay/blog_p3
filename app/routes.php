<?php

// Home page
$app->match('/', 'blog_p3\Controller\HomeController::indexAction') 
    ->bind('home');

// Blog page
$app->match('/blog', 'blog_p3\Controller\HomeController::blogAction')
    ->bind('blog');



// Article details with comments
$app->match('/article/{id}', 'blog_p3\Controller\HomeController::articleAction')
    ->bind('article');


// Login form
$app->get('/login', 'blog_p3\Controller\HomeController::loginAction')
    ->bind('login');




// Admin Home page
$app->get('/admin', 'blog_p3\Controller\AdminController::adminAction')
    ->bind('admin');




// Add a new article
$app->match('/admin/article/add', 'blog_p3\Controller\AdminController::addArticleAction')
    ->bind('admin_article_add');




// Edit an existing article
$app->match('/admin/article/{id}/edit', 'blog_p3\Controller\AdminController::editArticleAction')
    ->bind('admin_article_edit');



// Remove an article
$app->get('/admin/article/{id}/delete', 'blog_p3\Controller\AdminController::removeArticleAction')
    ->bind('admin_article_delete');



// Remove a comment
$app->get('/admin/comment/{id}/delete', 'blog_p3\Controller\AdminController::removeCommentAction')
    ->bind('admin_comment_delete');



//Report a comment
$app->get('article/comment/{id}/report', 'blog_p3\Controller\HomeController::reportCommentAction')
    ->bind('report_comment');

//Send a newsletter
$app->match('/admin/newsletter', 'blog_p3\Controller\AdminController::sendNewsletterAction')
    ->bind('admin_newsletter_add');

// Remove a subscribers
$app->get('/admin/newsletter/{id}/delete', 'blog_p3\Controller\AdminController::removeSubscriberAction')
    ->bind('admin_subscriber_delete');


//Page contact

$app->match('/contact', 'blog_p3\Controller\HomeController::contactAction')
    ->bind('contact');












