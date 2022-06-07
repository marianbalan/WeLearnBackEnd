<?php

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssignmentRepository::class)
 */
class Assignment
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
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $dueTo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private ?string $requirementFilePath;

    /**
     * @ORM\ManyToOne(targetEntity=Subject::class, inversedBy="assignments")
     * @ORM\JoinColumn(nullable=false)
     */
    private Subject $subject;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $closed;

    /**
     * @var Collection<int, AssignmentResponse>
     *
     * @ORM\OneToMany(targetEntity=AssignmentResponse::class, mappedBy="assignment", cascade={"remove"})
     */
    private Collection $assignmentResponses;

    public function __construct(
        string $title,
        ?string $description,
        \DateTimeInterface $date,
        \DateTimeInterface $dueTo
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->dueTo = $dueTo;
        $this->assignmentResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDueTo(): ?\DateTimeInterface
    {
        return $this->dueTo;
    }

    public function setDueTo(\DateTimeInterface $dueTo): self
    {
        $this->dueTo = $dueTo;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRequirementFilePath(): ?string
    {
        return $this->requirementFilePath;
    }

    public function setRequirementFilePath(?string $requirementFilePath): self
    {
        $this->requirementFilePath = $requirementFilePath;

        return $this;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * @return Collection<int, AssignmentResponse>
     */
    public function getAssignmentResponses(): Collection
    {
        return $this->assignmentResponses;
    }

    public function addAssignmentResponse(AssignmentResponse $assignmentResponse): self
    {
        if (!$this->assignmentResponses->contains($assignmentResponse)) {
            $this->assignmentResponses[] = $assignmentResponse;
            $assignmentResponse->setAssignment($this);
        }

        return $this;
    }

    public function removeAssignmentResponse(AssignmentResponse $assignmentResponse): self
    {
        if ($this->assignmentResponses->removeElement($assignmentResponse)) {
            // set the owning side to null (unless already changed)
            if ($assignmentResponse->getAssignment() === $this) {
                $assignmentResponse->setAssignment(null);
            }
        }

        return $this;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
