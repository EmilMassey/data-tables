<?php

namespace App\Form;

use App\Form\DTO\Table;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class EditTableForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa tabeli',
            ])
            ->add('users', TableUsersForm::class, [
                'label' => false,
                'constraints' => [new Valid()],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Uaktualnij tabelę',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Table::class);
    }
}
