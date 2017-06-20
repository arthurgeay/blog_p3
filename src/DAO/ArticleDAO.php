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
     * Saves an article into the database.
     *
     * @param \MicroCMS\Domain\Article $article The article to save
     */
    public function save(Article $article) {
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent(),
            );

        if ($article->getId()) {
            // The article has already been saved : update it
            $this->getDb()->update('t_article', $articleData, array('art_id' => $article->getId()));
        } else {
            // The article has never been saved : insert it
            $this->getDb()->insert('t_article', $articleData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    /**
     * Removes an article from the database.
     *
     * @param integer $id The article id.
     */
    public function delete($id) {
        // Delete the article
        $this->getDb()->delete('t_article', array('art_id' => $id));
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