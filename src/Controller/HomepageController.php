<?php

namespace App\Controller;

use App\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function index(): RedirectResponse
    {
        if (null === $user = $this->getUser()) {
            return $this->redirectToRoute('login');
        }

        if ($user instanceof UserInterface && $user->isAdmin()) {
            return $this->redirectToRoute('admin_table_list');
        }

        return $this->redirectToRoute('table_list');
    }
}
