<?php

namespace App\Controller\Admin;

use App\Command\ChangeUserPassword;
use App\Command\CreateUser;
use App\Command\DeleteUser;
use App\Entity\UserInterface;
use App\Form\CreateUserForm;
use App\Form\DTO\User;
use App\Password\PasswordGeneratorInterface;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;
    /**
     * @var PasswordGeneratorInterface
     */
    private $passwordGenerator;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        PasswordGeneratorInterface $passwordGenerator,
        UserRepositoryInterface $userRepository
    ) {
        $this->messageBus = $messageBus;
        $this->passwordGenerator = $passwordGenerator;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="admin_user_list", methods={"GET"})
     */
    public function listUsers(): Response
    {
        $users = $this->userRepository->getAll();

        // do not list current user
        $users = \array_filter($users, function (UserInterface $user) {
            return $user !== $this->getUser();
        });

        return $this->render('admin/user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/create", name="admin_user_create")
     */
    public function createUser(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordGenerator->generate(10);
            $this->messageBus->dispatch(new CreateUser($user->email, $password, $user->admin));

            $this->addFlash(
                'success',
                \sprintf(
                    'Dodano %s. Hasło: %s',
                    $user->admin ? 'Administratora' : 'Użytkownika',
                    $password
                )
            );

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{email}/reset-password", name="admin_user_reset-password", methods={"POST"})
     */
    public function resetUserPassword(string $email, Request $request): RedirectResponse
    {
        if (null === $this->userRepository->get($email)) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('user.reset_password', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $password = $this->passwordGenerator->generate(10);
        $this->messageBus->dispatch(new ChangeUserPassword($email, $password));

        $this->addFlash(
            'success',
            \sprintf('Zmieniono hasło: %s', $password)
        );

        return $this->redirectToRoute('admin_user_list');
    }

    /**
     * @Route("/{email}/delete", name="admin_user_delete", methods={"POST"})
     */
    public function deleteUser(string $email, Request $request): RedirectResponse
    {
        if (null === $this->userRepository->get($email)) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('user.delete', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $this->messageBus->dispatch(new DeleteUser($email));

        $this->addFlash(
            'success',
            \sprintf('Usunięto użytkownika %s', $email)
        );

        return $this->redirectToRoute('admin_user_list');
    }
}
