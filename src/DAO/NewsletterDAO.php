<?php

namespace blog_p3\DAO;

use blog_p3\Domain\Newsletter;

class NewsletterDAO extends DAO
{
	/**
	 * Count the number of subscribers
	 * 
	 * @return int
	 */
	public function count()
	{
		$sql = 'SELECT COUNT(*) as nbOfSubscribers FROM t_newsletter';
		$result = $this->getDb()->fetchAll($sql);

		$nbOfSubscribers = array();
		foreach($result as $row)
		{
			$nbOfSubscribers[] = $row['nbOfSubscribers'];
		}

		$nbOfSubscribersString = implode($nbOfSubscribers);

		return $nbOfSubscribersString;
	}

	/**
     * Saves a user newsletter into the database.
     *
     * @param \blog_p3\Domain\Newsletter $newsletter The user newsletter to save
     */
	public function save(Newsletter $newsletter)
	{
		$newsletterData = array(
			'nws_id' => $newsletter->getId(),
			'nws_name' => $newsletter->getName(),
			'nws_email' => $newsletter->getEmail()
		);

		$this->getDb()->insert('t_newsletter', $newsletterData);
		$id = $this->getDb()->lastInsertId();
		$newsletter->setId($id);
	}

	/**
	 * Find subscribers
	 * 
	 * @return  array
	 */
	public function allMail()
	{
		$sql = 'SELECT nws_email FROM t_newsletter';
		$result = $this->getDb()->fetchAll($sql);

		$subscribers = array();
		foreach($result as $row)
		{
			$subscribers[] = $row['nws_email'];
		}
		
		return $subscribers;
	}

	/**
	 * Find all subscribers
	 * 
	 * @return  array
	 */
	public function findAll()
	{
		$sql = 'SELECT * FROM t_newsletter';
		$result = $this->getDb()->fetchAll($sql);

		$allSubscribers = array();
		foreach($result as $row)
		{
			$subscribersId = $row['nws_id'];
			$allSubscribers[$subscribersId] = $this->buildDomainObject($row);
		}

		return $allSubscribers;
	}

	public function delete($id)
	{
		$this->getDb()->delete('t_newsletter', array('nws_id' => $id));
	}


	/**
     * Creates an User Newsletter object based on a DB row.
     *
     * @param array $row The DB row containing User Newsletter data.
     * @return \blog_p3\Domain\Newsletter
     */
	protected function buildDomainObject(array $row)
	{
		$newsletter = new Newsletter();
		$newsletter->setId($row['nws_id']);
		$newsletter->setName($row['nws_name']);
		$newsletter->setEmail($row['nws_email']);

		return $newsletter;
	}
}