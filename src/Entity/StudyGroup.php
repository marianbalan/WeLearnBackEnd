<?php

namespace App\Entity;

use App\Repository\StudyGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudyGroupRepository::class)
 */
class StudyGroup
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
     * @ORM\Column(type="integer")
     */
    private int $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $specialization;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="studyGroups", cascade={"persist"})
     */
    private School $school;

    /**
     * @var Collection<int, Subject>
     *
     * @ORM\OneToMany(targetEntity=Subject::class, mappedBy="studyGroup")
     */
    private Collection $subjects;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist"})
     */
    private ?User $classMaster;

    /**
     * @var Collection<int, User>
     *
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="studyGroup")
     */
    private Collection $students;

    public function __construct(
        string $name,
        int $number,
        string $specialization,
    ) {
        $this->name = $name;
        $this->number = $number;
        $this->specialization = $specialization;
        $this->subjects = new ArrayCollection();
        $this->students = new ArrayCollection();
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

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getSchool(): School
    {
        return $this->school;
    }

    public function setSchool(School $school): self
    {
        $this->school = $school;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    /**
     * @param Collection<int, Subject> $subjects
     */
    public function setSubjects(Collection $subjects): self
    {
        $this->subjects = $subjects;

        return $this;
    }

    public function getClassMaster(): ?User
    {
        return $this->classMaster;
    }

    public function setClassMaster(?User $classMaster): self
    {
        $this->classMaster = $classMaster;

        return $this;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): StudyGroup
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(User $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setStudyGroup($this);
        }

        return $this;
    }

    public function removeStudent(User $student): self
    {
        if ($this->students->removeElement($student) && $student->getStudyGroup() === $this) {
            $student->setStudyGroup(null);
        }

        return $this;
    }
}
