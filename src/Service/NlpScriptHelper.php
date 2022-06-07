<?php

namespace App\Service;

use App\Dto\Dto\NlpInput;
use App\Dto\ViewModel\NlpOutput;
use App\Service\Exception\ServiceException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class NlpScriptHelper
{
    private const MIN_SIMILARITY_RATE = 0.3;

    public function __construct(
        private string $pythonInterpreterPath,
        private string $similarityScriptPath,
        private SerializerInterface $serializer,
        private FileService $fileService,
    ) {
    }

    /**
     * @param NlpInput[] $inputs
     * @return NlpOutput[]
     */
    public function getTeacherSimilarityScriptOutput(array $inputs): array
    {
        return $this->runSimilarityScript($inputs, 'all');
    }

    /**
     * @param NlpInput[] $otherInputs
     * @return NlpOutput[]
     */
    public function getStudentSimilarityScriptOutput(NlpInput $studentInput, array $otherInputs): array
    {
        $nlpOutputs = $this->runSimilarityScript(
            [
                'studentInput' => $studentInput,
                'otherInputs' => $otherInputs
            ],
            'one'
        );

        $similarityRates = \array_map(
            fn (NlpOutput $output) => $output->getSimilarityRate(),
            $nlpOutputs
        );

        $maxSimilarity = \count($similarityRates) > 0 ? \max($similarityRates) : 0;

        $output = \array_values(\array_filter(
            $nlpOutputs,
            fn (NlpOutput $output) => $output->getSimilarityRate() === $maxSimilarity
        ));

        return \count($output) > 0 ? [$output[0]] : [];

//        return [
//            \array_values(\array_filter(
//                $nlpOutputs,
//                fn (NlpOutput $output) => $output->getSimilarityRate() === $maxSimilarity
//            ))[0] ?? []
//        ];
    }

    /**
     * @param array<mixed> $inputs
     * @return NlpOutput[]
     * @throws \JsonException
     */
    public function runSimilarityScript(array $inputs, string $type): array {
        $output = [];
        $file = $this->fileService->writeNlpInputsFile(\json_encode($inputs, JSON_THROW_ON_ERROR));

        \exec("{$this->pythonInterpreterPath} {$this->similarityScriptPath} {$file['name']} {$type}", $output);

        $this->fileService->removeFile($file['location']);

        if (0 === \count($output)) {
            throw new ServiceException('An error has occurred while computing files similarity.');
        }

        return \array_values(\array_filter(
            $this->serializer->deserialize(
                str_replace("'", '"', $output[0]),
                NlpOutput::class . '[]',
                JsonEncoder::FORMAT
            ),
            fn (NlpOutput $output) => $output->getSimilarityRate() > self::MIN_SIMILARITY_RATE
        ));
    }
}