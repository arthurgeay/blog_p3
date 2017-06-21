<?php

namespace blog_p3\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    	$builder->add('parent_id', HiddenType::class);
    	$builder->add('author', TextType::class);
        $builder->add('content', TextareaType::class);
    }

    public function getName()
    {
        return 'comment';
    }
}
