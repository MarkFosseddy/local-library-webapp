<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AuthorFormType extends AbstractType
{
    private $attrs = ['required' => false, 'label' => false];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, $this->attrs)
            ->add('last_name', TextType::class, $this->attrs)
            ->add('date_of_birth', DateType::class, [...$this->attrs, 'widget' => 'single_text'])
            ->add('date_of_death', DateType::class, [...$this->attrs, 'widget' => 'single_text']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
