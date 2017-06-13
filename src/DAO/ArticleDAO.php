<?php

namespace blog_p3\DAO;

use Doctrine\DBAL\Connection;
use blog_p3\Domain\Article;
use \IntlDateFormatter;
use \DateTime;

class ArticleDAO extends DAO
{
    

    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select art_id, art_title, art_content, DATE_FORMAT(art_date, '%d/%m/%Y') as date from t_article order by art_id desc";
        $result = $this->getDb()->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Returns an article matching the supplied id whith date of publication
     *
     * @param integer $id
     *
     * @return \blog_p3\Domain\Article|throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "select art_id, art_title, art_content, art_date as date from t_article where art_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        // Format the date in French
        $formatter = new IntlDateFormatter('fr_FR',IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'Europe/Paris',
                IntlDateFormatter::GREGORIAN );
        $date = new DateTime($row['date']);

        $row['date'] = $formatter->format($date);
        
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("Aucun article n'a l'identifiant : " . $id);
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \MicroCMS\Domain\Article
     */
    protected function buildDomainObject(array $row) {
        $article = new Article();
        $article->setId($row['art_id']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);
        $article->setDate($row['date']);
        return $article;
    }
}