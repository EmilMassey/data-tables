<?php

namespace App\Controller;

use App\CSV\ReaderInterface;
use App\Entity\UserInterface;
use App\Repository\TableRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/table")
 */
class TableController extends AbstractController
{
    /**
     * @var TableRepositoryInterface
     */
    private $repository;
    /**
     * @var ReaderInterface
     */
    private $reader;

    public function __construct(TableRepositoryInterface $repository, ReaderInterface $reader)
    {
        $this->repository = $repository;
        $this->reader = $reader;
    }

    /**
     * @Route("/", name="table_list")
     */
    public function list(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_table_list');
        }

        /** @var UserInterface $user */
        $user = $this->getUser();
        $tables = $this->repository->getAllByUser($user);

        return $this->render('table/list.html.twig', [
            'tables' => $tables,
        ]);
    }

    /**
     * @Route("/{id}", name="table_show")
     */
    public function show(string $id): Response
    {
        if (null === $table = $this->repository->get($id)) {
            throw $this->createNotFoundException();
        }

        if (!$this->isGranted('ROLE_ADMIN') && !\in_array($this->getUser(), $table->getUsers(), true)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('table/show.html.twig', [
            'name' => $table->getName(),
            'content' => $this->reader->read($table->getFilePath()),
        ]);
    }
}
