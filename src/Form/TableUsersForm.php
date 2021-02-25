<?php

namespace App\Form;

use App\Form\DataMapper\TableUsersDataMapper;
use App\Form\DTO\TableUsers;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableUsersForm extends AbstractType
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('allUsers', CheckboxType::class, [
                'label' => 'admin.all_users_access',
                'required' => false,
                'attr' => [
                    'class' => 'all-users-checkbox',
                ],
            ])
            ->add('users', ChoiceType::class, [
                'label' => 'admin.users_access',
                'choices' => $this->getChoices(),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'row_attr' => [
                    'class' => 'users-checkbox-group',
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var TableUsers|null $data */
                $data = $event->getData();

                if (null !== $data) {
                    $data->allUsers = \count($data->users) === \count($this->getChoices());
                    $event->setData($data);
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var TableUsers $data */
                $data = $event->getData();

                if ($data->allUsers) {
                    $data->users = array_values($this->getChoices());
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', TableUsers::class);
    }

    private function getChoices(): array
    {
        $choices = [];

        foreach ($this->userRepository->getAllNonAdmins() as $user) {
            $choices[$user->getEmail()] = $user->getEmail();
        }

        return $choices;
    }
}
