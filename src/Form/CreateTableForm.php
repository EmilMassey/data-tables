<?php

namespace App\Form;

use App\Form\DTO\Table;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTableForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa tabeli',
            ])
            ->add('file', FileType::class, [
                'label' => 'Plik',
                'attr' => [
                    'accept' => 'text/csv',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Dodaj tabelę',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Table::class);
    }
}
