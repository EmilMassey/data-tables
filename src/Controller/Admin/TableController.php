<?php

namespace App\Controller\Admin;

use App\Command\ChangeTableName;
use App\Command\CreateTable;
use App\Command\DeleteTable;
use App\Command\SetTableUsers;
use App\Form\CreateTableForm;
use App\Form\DTO\Table;
use App\Form\EditTableForm;
use App\Repository\TableRepositoryInterface;
use App\Uploader\TableUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * @Route("/admin/table")
 */
class TableController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;
    /**
     * @var TableRepositoryInterface
     */
    private $tableRepository;
    /**
     * @var TableUploader
     */
    private $tableUploader;

    public function __construct(
        MessageBusInterface $messageBus,
        TableRepositoryInterface $tableRepository,
        TableUploader $tableUploader
    ) {
        $this->messageBus = $messageBus;
        $this->tableRepository = $tableRepository;
        $this->tableUploader = $tableUploader;
    }
    /**
     * @Route("/", name="admin_table_list", methods={"GET"})
     */
    public function listTables(): Response
    {
        $tables = $this->tableRepository->getAll();

        return $this->render('admin/table/list.html.twig', [
            'tables' => $tables,
        ]);
    }

    /**
     * @Route("/create", name="admin_table_create")
     */
    public function createTable(Request $request): Response
    {
        $table = new Table();
        $form = $this->createForm(CreateTableForm::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tableUploader->setFilename($table->name);
            $filepath = $this->tableUploader->upload($table->file);

            $id = Uuid::v4();
            $this->messageBus->dispatch(new CreateTable($id, $table->name, $filepath));
            $this->messageBus->dispatch(new SetTableUsers($id, $table->users->users));

            $this->addFlash(
                'success',
                \sprintf('Dodano tabelę %s.', $table->name)
            );

            return $this->redirectToRoute('admin_table_list');
        }

        return $this->render('admin/table/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_table_edit")
     */
    public function editTable(string $id, Request $request): Response
    {
        if (null === $table = $this->tableRepository->get($id)) {
            throw $this->createNotFoundException();
        }

        $tableDto = Table::createFromEntity($table);
        $form = $this->createForm(EditTableForm::class, $tableDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageBus->dispatch(new ChangeTableName($id, $tableDto->name));
            $this->messageBus->dispatch(new SetTableUsers($id, $tableDto->users->users));

            $this->addFlash(
                'success',
                \sprintf('Zmieniono tabelę %s.', $tableDto->name)
            );

            return $this->redirectToRoute('admin_table_list');
        }

        return $this->render('admin/table/edit.html.twig', [
            'table' => $table,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_table_delete", methods={"POST"})
     */
    public function deleteTable(string $id, Request $request): RedirectResponse
    {
        if (null === $table = $this->tableRepository->get($id)) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('table.delete', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $this->messageBus->dispatch(new DeleteTable($id));

        $this->addFlash(
            'success',
            \sprintf('Usunięto tabelę "%s" (%s)', $table->getName(), $id)
        );

        return $this->redirectToRoute('admin_table_list');
    }
}
