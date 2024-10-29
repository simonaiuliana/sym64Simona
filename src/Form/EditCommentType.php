<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('commentMessage', TextareaType::class, ['attr' => ['placeholder' => 'Votre Commentaire', 'rows' => 5, 'cols' => 40]])
        ->add('user', entitytype::class, [
            'class' => user::class,
            'choice_label' => 'username',
        ])
        ->add('article', entitytype::class, [
            'class' => Article::class,
            'choice_label' => 'title',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
