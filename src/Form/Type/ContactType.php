<?php

namespace blog_p3\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('title', TextType::class, array('label' => 'Titre'));
    	$builder->add('mail', EmailType::class, array(
                    'label' => 'Adresse email',
                    'required' => true,
                    'invalid_message' => 'Cette email n\'est pas valide.',
                    'constraints' => new Assert\Email(['checkMX' => true])));

        $builder->add('content', TextareaType::class, array('label' => 'Contenu'));
    }

    public function getName()
    {
        return 'contact';
    }
}
