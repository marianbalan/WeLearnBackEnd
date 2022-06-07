<?php

namespace App\Entity;

use App\Repository\NonAttendanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NonAttendanceRepository::class)
 */
class NonAttendance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $date;

    /**
     * @ORM\ManyToOne(targetEntity=Subject::class, inversedBy="nonAttendances")
     */
    private Subject $subject;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="nonAttendances")
     */
    private User $student;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $motivated;

    public function __construct(
        \DateTimeInterface $date,
        bool $motivated,
    ) {
        $this->date = $date;
        $this->motivated = $motivated;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): self
    {
        $this->subject = $subject;

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

    public function getMotivated(): bool
    {
        return $this->motivated;
    }

    public function setMotivated(bool $motivated): self
    {
        $this->motivated = $motivated;

        return $this;
    }
}
