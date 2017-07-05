<?php

namespace blog_p3\DAO;

use blog_p3\Domain\Mail;

class MailDAO extends DAO
{

	/**
	 * Save mail in database
	 * 
	 */
	public function save(Mail $mail)
	{
		$mailData = array(
			'mail_id' => $mail->getId(),
			'mail_title' => $mail->getTitle(),
			'mail_content' => $mail->getContent()
		);

		$this->getDb()->insert('t_mail', $mailData);
		$id = $this->getDb()->lastInsertId();
		$mail->setId($id);
	}

	/**
	 * 
	 * Obtain the latest newsletter
	 * 
	 * @return blog_p3\Domain\Mail
	 */
	public function find()
	{
		$id = $this->getDb()->lastInsertId();
		$sql = "SELECT * FROM t_mail WHERE mail_id = ?";

		$result = $this->getDb()->fetchAssoc($sql, array($id));

		if ($result)
		{
			return $this->buildDomainObject($result);
		} 
	}

	public function delete($id)
	{
		$this->getDb()->delete('t_mail', array('mail_id' => $id));
	}

	/**
     * Creates an Newsletter object based on a DB row.
     *
     * @param array $row The DB row containing mail.
     * @return \blog_p3\Domain\Mail
     */
	protected function buildDomainObject(array $row)
	{
		$mail = new Mail();
		$mail->setId($row['mail_id']);
		$mail->setTitle($row['mail_title']);
		$mail->setContent($row['mail_content']);

		return $mail;
	}
}