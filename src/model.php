<?php

// Return all articles
function getArticles() {
    $bdd = new PDO('mysql:host=localhost;dbname=p3_blog;charset=utf8', 'root', 'root');
    $articles = $bdd->query('select * from t_article order by art_id desc');
    return $articles;
}