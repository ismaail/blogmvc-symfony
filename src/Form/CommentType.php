<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, EmailType, TextareaType, SubmitType };

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('content', TextareaType::class)
            ->add('submit', SubmitType::class, ['label' => 'Send'])
            ;
    }
}
