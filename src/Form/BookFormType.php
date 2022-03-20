<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookFormType extends AbstractType
{
    private $attrs = ['required' => false, 'label' => false];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->attrs)
            ->add('summary', TextareaType::class, $this->attrs)
            ->add('ISBN', TextType::class, $this->attrs)
            ->add('author', EntityType::class, [
                ...$this->attrs,
                'class' => Author::class,
                'placeholder' => 'Choose an author',
                'choice_label' => 'name'
            ])
            ->add('genres', EntityType::class, [
                ...$this->attrs,
                'class' => Genre::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Book::class]);
    }
}
