<?php

namespace blog_p3\DAO;

use blog_p3\Domain\Comment;
use \IntlDateFormatter;
use \DateTime;

class CommentDAO extends DAO 
{
    /**
     * @var \blog_p3\DAO\ArticleDAO
     */
    private $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    /**
     * Return a list of all comments for an article, sorted by date (most recent last).
     *
     * @param integer $articleId The article id.
     *
     * @return array A list of all comments for the article.
     */
    public function findAllByArticle($articleId) {
        // The associated article is retrieved only once
        $article = $this->articleDAO->find($articleId);

        // art_id is not selected by the SQL query
        // The article won't be retrieved during domain objet construction
        $sql = "select com_id, com_content, com_author, com_date, parent_id from t_comment where art_id=? order by com_id";
        $result = $this->getDb()->fetchAll($sql, array($articleId));

    
        // Convert query result to an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $comId = $row['com_id'];

        // Format the date in French 
            $formatter = new IntlDateFormatter('fr_FR',IntlDateFormatter::FULL,
                IntlDateFormatter::SHORT,
                'Europe/Paris',
                IntlDateFormatter::GREGORIAN );

            $date = new DateTime($row['com_date']);
            $row['com_date'] = $formatter->format($date);

            $comment = $this->buildDomainObject($row);
            // The associated article is defined for the constructed comment
            $comment->setArticle($article);
            $comments[$comId] = $comment;
        }

        $parentcomments = array_filter($comments, function($comment)
        {
            return $comment->getParentId() === NULL;
        });

        foreach ($parentcomments as $parentcomment) {
            $this->setChildren($comments, $parentcomment);
        }

        return $parentcomments;

    }

    public function setChildren($allcomments, $comment)
    {
        $children = array_filter($allcomments, function($childcomment) use ($comment)
        {
            return $childcomment->getParentId() === $comment->getId();
        });

        $comment->setChildren($children);

        foreach ($children as $childcomment) {
            $this->setChildren($allcomments, $childcomment);
        }

    }



    /**
     * Saves a comment into the database.
     *
     * @param \blog_p3\Domain\Comment $comment The comment to save
     */
    public function save(Comment $comment) {

        $now = $this->getDb()->fetchColumn("SELECT NOW()");  // Get the date of comment

        $commentData = array(
            'art_id' => $comment->getArticle()->getId(),
            'com_author' => $comment->getAuthor(),
            'com_content' => $comment->getContent(),
            'com_date' => $now,
            'parent_id' => $comment->getParentId()
            );


        if ($comment->getId()) {
            // The comment has already been saved : update it
            $this->getDb()->update('t_comment', $commentData, array('com_id' => $comment->getId()));
        } else {
            
            // The comment has never been saved : insert it
            $this->getDb()->insert('t_comment', $commentData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    /**
     * Creates an Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \blog_p3\Domain\Comment
     */
    protected function buildDomainObject(array $row) {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setContent($row['com_content']);
        $comment->setAuthor($row['com_author']);
        $comment->setDate($row['com_date']);
        $comment->setParentId($row['parent_id']);

        if (array_key_exists('art_id', $row)) {
            // Find and set the associated article
            $articleId = $row['art_id'];
            $article = $this->articleDAO->find($articleId);
            $comment->setArticle($article);
        }
        
        return $comment;
    }
}