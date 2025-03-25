<?php

declare(strict_types=1);

namespace PhpYabs\Form;

use PhpYabs\Entity\Book;
use PhpYabs\Entity\Rate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isbn')
            ->add('title')
            ->add('author')
            ->add('publisher')
            ->add('price', MoneyType::class)
            ->add('rate', EnumType::class, [
                'class' => Rate::class,
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, ['label' => 'Aggiungi'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
