<?php

namespace blog_p3\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                    'label' => 'Votre eMail ',
                    'attr'  => ['placeholder' => '(ne sera pas publié)'],
                    'required' => true,
                    'invalid_message' => 'Cette email n\'est pas valide.',
                    'constraints' => new Assert\Email(['checkMX' => true])))

            ->add('name', TextType::class, array('label' => 'Nom'));
    }

    public function getName()
    {
        return 'newsletter';
    }
}