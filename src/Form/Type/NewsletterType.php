<?php

namespace blog_p3\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, array('label' => 'Email'))
            ->add('name', TextType::class, array('label' => 'Nom'));
    }

    public function getName()
    {
        return 'newsletter';
    }
}