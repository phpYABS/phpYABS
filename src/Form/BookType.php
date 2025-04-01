<?php

declare(strict_types=1);

namespace PhpYabs\Form;

use PhpYabs\Entity\Book;
use PhpYabs\Entity\Rate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tbbc\MoneyBundle\Form\Type\MoneyType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isbn', options: ['label' => 'book.fields.isbn'])
            ->add('title', options: ['label' => 'book.fields.title'])
            ->add('author', options: ['label' => 'book.fields.author'])
            ->add('publisher', options: ['label' => 'book.fields.publisher'])
            ->add('price', MoneyType::class, options: ['label' => 'book.fields.price'])
            ->add('rate', EnumType::class, [
                'class' => Rate::class,
                'choice_label' => 'name',
                'label' => 'book.fields.rating',
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
