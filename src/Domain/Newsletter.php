<?php

namespace blog_p3\Domain;

class Newsletter
{
	/**
     * Newsletter user id.
     *
     * @var integer
     */
    private $id;

    /**
     * Name of newsletter user
     * 
     * @var string
     */
    private $name;

    /**
     * Email of newsletter user
     * 
     * @var  string
     */
    private $email;

    public function getId()
    {
    	return $this->id;
    }

    public function setId($id)
    {
    	$this->id = $id;
    	return $this;
    }

    public function getName()
    {
    	return $this->name;
    }

    public function setName($name)
    {
    	$this->name = $name;
    	return $this;
    }

    public function getEmail()
    {
    	return $this->email;
    }

    public function setEmail($email)
    {
    	$this->email = $email;
    	return $this;
    }
}