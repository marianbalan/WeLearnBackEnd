<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Utils\UserRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @var string[]
     *
     * @ORM\Column(type="json")
     */
    private array $roles;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $pin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $phoneNumber;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="users")
     */
    private School $school;

    /**
     * @var Collection<int, Subject>
     *
     * @ORM\OneToMany(targetEntity=Subject::class, mappedBy="teacher")
     */
    private Collection $teachingSubjects;

    /**
     * @var Collection<int, NonAttendance>
     *
     * @ORM\OneToMany(targetEntity=NonAttendance::class, mappedBy="student", cascade={"remove"})
     */
    private Collection $nonAttendances;

    /**
     * @var Collection<int, Grade>
     *
     * @ORM\OneToMany(targetEntity=Grade::class, mappedBy="student", cascade={"remove"})
     */
    private Collection $grades;

    /**
     * @ORM\ManyToOne(targetEntity=StudyGroup::class, inversedBy="students")
     */
    private ?StudyGroup $studyGroup;

    /**
     * @ORM\Column(type="boolean", options={"defaut": 0})
     */
    private bool $activated;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private ?string $activationToken;

    /**
     * @var Collection<int, AssignmentResponse>
     *
     * @ORM\OneToMany(targetEntity=AssignmentResponse::class, mappedBy="student", cascade={"remove"})
     */
    private Collection $assignmentResponses;

    /**
     * @param string[] $roles
     */
    public function __construct(
        string $email,
        array $roles,
        string $password,
        string $firstName,
        string $lastName,
        string $pin,
        string $phoneNumber,
    ) {
        $this->email = $email;
        $this->roles = $roles;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->pin = $pin;
        $this->phoneNumber = $phoneNumber;

        $this->teachingSubjects = new ArrayCollection();
        $this->nonAttendances = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->assignmentResponses = new ArrayCollection();
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setRoleUser(): self
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        $this->roles = \array_unique($roles);

        return $this;
    }

    public function addRole(string $role): self
    {
        $roles = $this->roles;
        $roles[] = $role;

        $this->roles = \array_unique($roles);

        return $this;
    }

    public function removeRole(string $role): self
    {
        $roles = \array_filter(
            $this->roles,
            fn (string $userRole) => $userRole !== $role
        );

        $this->roles = \array_unique($roles);

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPin(): string
    {
        return $this->pin;
    }

    public function setPin(string $pin): self
    {
        $this->pin = $pin;

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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
    public function getTeachingSubjects(): Collection
    {
        return $this->teachingSubjects;
    }

    /**
     * @param Collection<int, Subject> $teachingSubjects
     */
    public function setTeachingSubjects(Collection $teachingSubjects): self
    {
        $this->teachingSubjects = $teachingSubjects;

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

    public function getStudyGroup(): ?StudyGroup
    {
        return $this->studyGroup;
    }

    public function setStudyGroup(?StudyGroup $studyGroup): self
    {
        $this->studyGroup = $studyGroup;

        return $this;
    }

    public function getActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

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
            $assignmentResponse->setStudent($this);
        }

        return $this;
    }

    public function removeAssignmentResponse(AssignmentResponse $assignmentResponse): self
    {
        if ($this->assignmentResponses->removeElement($assignmentResponse)) {
            // set the owning side to null (unless already changed)
            if ($assignmentResponse->getStudent() === $this) {
                $assignmentResponse->setStudent(null);
            }
        }

        return $this;
    }
}
