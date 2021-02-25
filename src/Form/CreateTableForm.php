<?php

namespace App\Form;

use App\Form\DTO\Table;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class CreateTableForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'admin.table_name',
            ])
            ->add('file', FileType::class, [
                'label' => 'admin.file',
                'attr' => [
                    'accept' => 'text/csv',
                ],
            ])
            ->add('users', TableUsersForm::class, [
                'label' => false,
                'constraints' => [new Valid()],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'admin.create_table',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Table::class);
    }
}
