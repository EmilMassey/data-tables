<?php

namespace App\Tests\Handler;

use App\Command\CreateTable;
use App\Entity\Table;
use App\Handler\CreateTableHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CreateTableHandlerTest extends TestCase
{
    public function test_persist()
    {
        $table = new Table('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', __FILE__);

        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($table);

        $handler = new CreateTableHandler($entityManager);

        $handler(new CreateTable('b9df4794-268b-499c-9fea-b4e4f5bcb2ef', 'test', __FILE__));
    }
}
