<?php

namespace App\Tests\Handler;

use App\Command\ChangeTableName;
use App\Entity\Table;
use App\Handler\ChangeTableNameHandler;
use App\Repository\TableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ChangeTableNameHandlerTest extends TestCase
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

        $handler = new ChangeTableNameHandler($repository, $entityManager);

        $handler(new ChangeTableName('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'name'));
    }

    public function test_persists()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', 'test.csv');

        $repository = $this->getMockBuilder(TableRepositoryInterface::class)->getMock();
        $repository
            ->method('get')
            ->willReturn($table);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($table);

        $handler = new ChangeTableNameHandler($repository, $entityManager);

        $handler(new ChangeTableName('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'new-name'));

        $this->assertSame('new-name', $table->getName());
    }
}
