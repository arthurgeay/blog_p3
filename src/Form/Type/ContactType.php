<?php

namespace blog_p3\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('title', TextType::class, array('label' => 'Titre'));
    	$builder->add('mail', TextType::class, array('label' => 'Adresse mail'));
        $builder->add('content', TextareaType::class, array('label' => 'Contenu'));
    }

    public function getName()
    {
        return 'contact';
    }
}
