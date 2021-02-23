<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(inverseJoinColumns={@ORM\JoinColumn(name="user_email", referencedColumnName="email")})
     */
    private $users;

    public function __construct(string $id, string $name, string $filepath)
    {
        $this->id = $id;
        $this->name = $name;
        $this->filepath = $filepath;

        $this->users = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFilePath(): string
    {
        return $this->filepath;
    }

    public function clearUsers(): void
    {
        $this->users->clear();
    }

    public function addUser(UserInterface $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /** @return UserInterface[] */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }
}
