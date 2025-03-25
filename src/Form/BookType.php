<?php

declare(strict_types=1);

namespace PhpYabs\Form;

use PhpYabs\Entity\Book;
use PhpYabs\Entity\BuybackRate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('price')
            ->add('buybackRate', EntityType::class, [
                'class' => BuybackRate::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
