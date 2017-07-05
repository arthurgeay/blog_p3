<?php

namespace blog_p3\DAO;

use blog_p3\Domain\Contact;

class ContactDAO extends DAO
{

	/**
	 * Save mail form contact
	 * 
	 */
	public function save(Contact $contact)
	{
		$contactData = array('ct_mail' => $contact->getMail(),
			'ct_title' => $contact->getTitle(),
			'ct_content' => $contact->getContent());

		$this->getDb()->insert('t_contact', $contactData);
		$id = $this->getDb()->lastInsertId();
		$contact->setId($id);
	}

	public function find()
	{
		$id = $this->getDb()->lastInsertId();
		$sql = "SELECT * FROM t_contact WHERE ct_id = ?";

		$result = $this->getDb()->fetchAssoc($sql, array($id));

		if ($result)
		{
			return $this->buildDomainObject($result);
		}
	}

	public function delete($id)
	{
	     $this->getDb()->delete('t_contact', array('ct_id' => $id));
	}


	/**
	 * Build an Contact Object based on a db row
	 * 
	 * @return  blog_p3\Domain\Contact
	 */
	protected function buildDomainObject(array $row) {
        $contact = new Contact();
        $contact->setId($row['ct_id']);
        $contact->setMail($row['ct_mail']);
        $contact->setTitle($row['ct_title']);
        $contact->setContent($row['ct_content']);
        
        return $contact;
    }
}