<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <link href="blog.css" rel="stylesheet" />
    <title>Blog - Home</title>
</head>
<body>
    <header>
        <h1>Blog</h1>
    </header>
    <?php
    $bdd = new PDO('mysql:host=localhost;dbname=p3_blog;charset=utf8', 'root', 'root');
    $articles = $bdd->query('select * from t_article order by art_id desc');
    foreach ($articles as $article): ?>
    <article>
        <h2><?php echo $article['art_title'] ?></h2>
        <p><?php echo $article['art_content'] ?></p>
    </article>
    <?php endforeach ?>
    <footer class="footer">
    </footer>
</body>
</html>