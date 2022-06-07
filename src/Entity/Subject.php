<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubjectRepository::class)
 */
class Subject
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
     * @ORM\ManyToOne(targetEntity=StudyGroup::class, inversedBy="subjects")
     */
    private StudyGroup $studyGroup;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="teachingSubjects")
     */
    private User $teacher;

    /**
     * @var Collection<int, NonAttendance>
     *
     * @ORM\OneToMany(targetEntity=NonAttendance::class, mappedBy="subject", cascade={"remove"})
     */
    private Collection $nonAttendances;

    /**
     * @var Collection<int, Grade>
     *
     * @ORM\OneToMany(targetEntity=Grade::class, mappedBy="subject", cascade={"remove"})
     */
    private Collection $grades;

    /**
     * @var Collection<int, Assignment>
     *
     * @ORM\OneToMany(targetEntity=Assignment::class, mappedBy="subject", cascade={"remove"})
     */
    private Collection $assignments;

    public function __construct(
        string $name,
    ) {
        $this->name = $name;
        $this->nonAttendances = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->assignments = new ArrayCollection();
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

    public function getStudyGroup(): StudyGroup
    {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): self
    {
        $this->studyGroup = $studyGroup;

        return $this;
    }

    public function getTeacher(): User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection<int, NonAttendance>
     */
    public function getNonAttendances(): Collection
    {
        return $this->nonAttendances;
    }

    /**
     * @param Collection<int, NonAttendance> $nonAttendences
     */
    public function setNonAttendances(Collection $nonAttendences): self
    {
        $this->nonAttendances = $nonAttendences;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    /**
     * @param Collection<int, Grade> $grades
     */
    public function setGrades(Collection $grades): self
    {
        $this->grades = $grades;

        return $this;
    }

    /**
     * @return Collection<int, Assignment>
     */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): self
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments[] = $assignment;
            $assignment->setSubject($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): self
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getSubject() === $this) {
                $assignment->setSubject(null);
            }
        }

        return $this;
    }
}
