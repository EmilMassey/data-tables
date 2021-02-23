<?php

namespace App\Controller;

use App\CSV\ReaderInterface;
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
     * @Route("/{id}", name="table_show")
     */
    public function show(string $id): Response
    {
        if (null === $table = $this->repository->get($id)) {
            throw $this->createNotFoundException();
        }

        return $this->render('table/show.html.twig', [
            'name' => $table->getName(),
            'content' => $this->reader->read($table->getFilePath()),
        ]);
    }
}
