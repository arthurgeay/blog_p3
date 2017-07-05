<?php

namespace blog_p3\Domain;

class Contact
{	
	/**
	 * Id of contact
	 * 
	 * @var  int
	 */
	private $id;

	/**
	 * 
	 * Title of message
	 * 
	 * @var  string
	 */
	private $title;

	/**
	 * 
	 * Email of contact
	 * 
	 * @var  string
	 */
	private $mail;

	/**
	 * 
	 * Content of mail
	 * 
	 * @var  string
	 */
	private $content;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	public function getMail()
	{
		return $this->mail;
	}

	public function setMail($email)
	{
		$this->mail = $email;
		return $this;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}
}