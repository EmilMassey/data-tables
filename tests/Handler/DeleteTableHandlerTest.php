<?php

namespace App\Tests\Handler;

use App\Command\DeleteTable;
use App\Entity\Table;
use App\Handler\DeleteTableHandler;
use App\Repository\TableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DeleteTableHandlerTest extends TestCase
{
    public function test_throws_if_table_does_not_exist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $repository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('get')
            ->with('b9df4794-268b-499c-9fea-b4e4f5bcb2ef')
            ->willReturn(null);

        $handler = new DeleteTableHandler($repository, $entityManager);

        $handler(new DeleteTable('b9df4794-268b-499c-9fea-b4e4f5bcb2ef'));
    }

    public function test_removes()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');

        $repository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($table);

        $handler = new DeleteTableHandler($repository, $entityManager);

        $handler(new DeleteTable('b9df4794-268b-499c-9fea-b4e4f5bcb2ef'));
    }

    public function test_deletes_file()
    {
        $path = sys_get_temp_dir() . '/test.csv';
        touch($path);

        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', $path);

        $repository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn($table);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $handler = new DeleteTableHandler($repository, $entityManager);

        $handler(new DeleteTable('b9df4794-268b-499c-9fea-b4e4f5bcb2ef'));

        $this->assertFileNotExists($path);
    }
}
