<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tables")
 */
class Table implements TableInterface
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $filepath;

    public function __construct(string $id, string $name, string $filepath)
    {
        $this->id = $id;
        $this->name = $name;
        $this->filepath = $filepath;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilePath(): string
    {
        return $this->filepath;
    }
}
