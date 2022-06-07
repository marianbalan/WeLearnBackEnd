<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SchoolRepository::class)
 */
class School
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @var array<string, string>
     *
     * @ORM\Column(type="json")
     */
    private array $location;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $cui;

    /**
     * @var Collection<int, User>
     *
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="school")
     */
    private Collection $users;

    /**
     * @var Collection<int, StudyGroup>
     *
     * @ORM\OneToMany(targetEntity=StudyGroup::class, mappedBy="school")
     */
    private Collection $studyGroups;

    /**
     * @param array<string, string> $location
     */
    public function __construct(
        string $name,
        string $cui,
        array $location,
    ) {
        $this->name = $name;
        $this->cui = $cui;
        $this->location = $location;

        $this->users = new ArrayCollection();
        $this->studyGroups = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getLocation(): array
    {
        return $this->location;
    }

    /**
     * @param array<string, string> $location
     */
    public function setLocation(array $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getCui(): string
    {
        return $this->cui;
    }

    public function setCui(string $cui): self
    {
        $this->cui = $cui;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection<int, StudyGroup>
     */
    public function getStudyGroups(): Collection
    {
        return $this->studyGroups;
    }

    /**
     * @param Collection<int, StudyGroup> $studyGroups
     */
    public function setStudyGroups(Collection $studyGroups): self
    {
        $this->studyGroups = $studyGroups;

        return $this;
    }
}
