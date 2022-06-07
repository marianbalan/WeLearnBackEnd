<?php

namespace App\Dto\ViewModel;

use App\Dto\Dto\NlpInput;

class NlpOutput
{
    public function __construct(
        private NlpInput $student1,
        private NlpInput $student2,
        private float $similarityRate
    ) {
    }

    public function getStudent1(): NlpInput
    {
        return $this->student1;
    }

    public function getStudent2(): NlpInput
    {
        return $this->student2;
    }

    public function getSimilarityRate(): float
    {
        return $this->similarityRate;
    }
}