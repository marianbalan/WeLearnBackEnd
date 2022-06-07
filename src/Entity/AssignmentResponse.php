<?php

namespace App\Entity;

use App\Repository\AssignmentResponseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssignmentResponseRepository::class)
 */
class AssignmentResponse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $date;

    /**
     * @ORM\ManyToOne(targetEntity=Assignment::class, inversedBy="assignmentResponses")
     */
    private Assignment $assignment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assignmentResponses")
     */
    private User $student;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $filePath;

    public function __construct(
        \DateTimeInterface $date
    ) {
        $this->date = $date;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAssignment(): Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(Assignment $assignment): self
    {
        $this->assignment = $assignment;

        return $this;
    }

    public function getStudent(): User
    {
        return $this->student;
    }

    public function setStudent(User $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
