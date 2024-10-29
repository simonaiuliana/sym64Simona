<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; // Adăugat pentru câmpul 'published'
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Section;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType; // Adăugat pentru câmpul 'article_date_create'

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('text', TextareaType::class)
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'name', // Ajustează în funcție de proprietatea dorită
            ])
            ->add('article_date_create', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date of Creation', // Label pentru câmp
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => 'Published',
            ])
            ->add('save', SubmitType::class, ['label' => 'Save Article']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
