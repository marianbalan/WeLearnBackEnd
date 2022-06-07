<?php

namespace App\Dto\ViewModel;

class AssignmentViewModel
{
    /**
     * @param AssignmentResponseViewModel[] $responses
     */
    public function __construct(
        private int $id,
        private string $title,
        private ?string $description,
        private int $date,
        private int $dueTo,
        private SubjectViewModel $subject,
        private ?string $requirementFilePath,
        private bool $closed,
        private array $responses = [],
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getDueTo(): int
    {
        return $this->dueTo;
    }

    public function getSubject(): SubjectViewModel
    {
        return $this->subject;
    }

    public function getRequirementFilePath(): ?string
    {
        return $this->requirementFilePath;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @return AssignmentResponseViewModel[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param AssignmentResponseViewModel[] $responses
     */
    public function setResponses(array $responses): self
    {
        $this->responses = $responses;

        return $this;
    }
}