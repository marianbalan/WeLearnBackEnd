<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\NlpInput;
use App\Entity\AssignmentResponse;

class NlpInputMapper
{
    public static function assignmentResponseToNlpInput(AssignmentResponse $response): NlpInput
    {
        return new NlpInput(
            $response->getStudent()->getFirstName(),
            $response->getStudent()->getLastName(),
            $response->getStudent()->getPin(),
            $response->getFilePath()
        );
    }

    /**
     * @param AssignmentResponse[] $responses
     * @return NlpInput[]
     */
    public static function assignmentResponsesToNlpInputs(array $responses): array
    {
        return \array_map(
            fn (AssignmentResponse $response) => self::assignmentResponseToNlpInput($response),
            $responses
        );
    }
}