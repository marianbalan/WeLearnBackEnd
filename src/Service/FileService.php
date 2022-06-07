<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public function __construct(
        private string $dtoDirPath,
    ) {
    }

    public function upload(UploadedFile $file, string $dirPath, string $type = 'assignment', string $studentName = ''): string
    {
        $originalFilename = \pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $newFilename = match ($type) {
            'assignment' => \sprintf(
                '%s-%s.%s',
                $originalFilename,
                \uniqid('', true),
                $file->guessExtension()
            ),
            default => \sprintf(
                '%s.%s',
                $studentName,
                $file->guessExtension()
            ),
        };

        $file->move($dirPath, $newFilename);

        return $newFilename;
    }

    /**
     * @return array<string, string>
     */
    public function writeNlpInputsFile(string $content): array
    {
        $fileName = \uniqid('', true) . '.json';
        $fileLocation = \sprintf('%s/%s', $this->dtoDirPath, $fileName);

        $file = \fopen($fileLocation, 'wb');

        \fwrite($file, $content);
        \fclose($file);

        return [
            'name' => $fileName,
            'location' => $fileLocation,
        ];
    }

    public function removeFile(string $filePath): void
    {
        \unlink($filePath);
    }


}