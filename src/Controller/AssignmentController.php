<?php

namespace App\Controller;

use App\Dto\Dto\AssignmentDto;
use App\Dto\Dto\AssignmentResponseDto;
use App\Service\AssignmentService;
use App\Service\Exception\ServiceException;
use App\Service\FileService;
use App\Service\NlpScriptHelper;
use App\Service\TokenManagerService;
use App\Service\UserService;
use App\Utils\AccessValidator;
use App\Validator\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class AssignmentController extends AbstractController
{
    public function __construct(
        private string              $assignmentsDirPath,
        private string              $responsesDirPath,
        private AssignmentService   $assignmentService,
        private UserService         $userService,
        private TokenManagerService $tokenManagerService,
        private FileService         $fileService,
        private SerializerInterface $serializer,
        private DecoderInterface    $decoder,
        private NlpScriptHelper     $nlpHelper,
    ) {
    }

    /**
     * @Route("/assignments/file", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_STUDENT')", message="Access denied.")
     */
    public function downloadAssignmentFileByPath(Request $request): Response
    {
        $filePath = $request->query->get('path');
        if (null === $filePath) {
            return $this->json('No file path provided!', Response::HTTP_BAD_REQUEST);
        }

        $absoluteFilePath = $this->assignmentsDirPath . '/' . $filePath;
        $response = new BinaryFileResponse($absoluteFilePath);

        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        if ($mimeTypeGuesser->isGuesserSupported()) {
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($absoluteFilePath));
        } else {
            $response->headers->set('Content-Type', 'text/plain');
        }

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filePath
        );

        return $response;
    }

    /**
     * @Route("/assignments/user/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_STUDENT')", message="Access denied.")
     */
    public function getAssignmentsByUser(Request $request, int $id): Response
    {
        try {
            return $this->json(
                $this->assignmentService->getAssignmentsByUser($id)
            );
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignments", methods={"POST"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function addAssignment(Request $request): Response
    {
        try {
            $assignmentDto = $this->serializer->deserialize(
                $request->getContent(),
                AssignmentDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->assignmentService->addAssignment($assignmentDto));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignments/{id}/file", methods={"POST"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function addAssignmentAttachment(Request $request, int $id): Response
    {
        /** @var UploadedFile | null $file */
        $file = $request->files->get('thumbnail');
        if (null === $file) {
            return $this->json('Invalid file provided!', Response::HTTP_BAD_REQUEST);
        }

        $fileName = $this->fileService->upload($file, $this->assignmentsDirPath, 'assignment');
        try {
            $this->assignmentService->updateAssignmentAttachmentPath($id, $fileName);
        } catch (ServiceException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response(
            $fileName,
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/plain'
            ]
        );
    }

    /**
     * @Route("/assignments/{id}", methods={"PUT"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function updateAssignment(Request $request, int $id): Response
    {
        try {
            $assignmentDto = $this->serializer->deserialize(
                $request->getContent(),
                AssignmentDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->assignmentService->updateAssignment($id, $assignmentDto));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignments/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function removeAssignment(Request $request, int $id): Response
    {
        try {
            return $this->json(
                $this->assignmentService->removeAssignment($id)
            );

        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignments/{id}", methods={"PATCH"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function updateAssignmentClose(Request $request, int $id): Response
    {
        $data = $this->decoder->decode(
            $request->getContent(),
            JsonEncoder::FORMAT
        );

        if (false === \array_key_exists('closed', $data)) {
            return $this->json('Missing data!', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->assignmentService->updateAssignmentClosed($id, $data['closed']);

            return $this->json(['id' => $id]);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignment-responses", methods={"POST"})
     * @IsGranted("ROLE_STUDENT", message="Access denied!")
     */
    public function addAssignmentResponse(Request $request): Response
    {
        try {
            $assignmentResponseDto = $this->serializer->deserialize(
                $request->getContent(),
                AssignmentResponseDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->assignmentService->addAssignmentResponse($assignmentResponseDto));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignment-responses/{id}/file", methods={"POST"})
     * @IsGranted("ROLE_STUDENT", message="Access denied!")
     */
    public function addAssignmentResponseAttachment(Request $request, int $id): Response
    {
        $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));

        /** @var UploadedFile | null $file */
        $file = $request->files->get('thumbnail');
        if (null === $file) {
            return $this->json('Invalid file provided!', Response::HTTP_BAD_REQUEST);
        }

        $fileName = $token['firstName'] . $token['lastName'] . '-' . $token['pin'] . '-' . \uniqid('', true);
        $fileName = $this->fileService->upload($file, $this->responsesDirPath, 'response', $fileName);
        try {
            $this->assignmentService->updateAssignmentResponseAttachmentPath($id, $fileName);
        } catch (ServiceException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response(
            $fileName,
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/plain'
            ]
        );
    }

    /**
     * @Route("/assignment-responses/file", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_STUDENT')", message="Access denied.")
     */
    public function downloadAssignmentResponseFileByPath(Request $request): Response
    {
        $filePath = $request->query->get('path');
        if (null === $filePath) {
            return $this->json('No file path provided!', Response::HTTP_BAD_REQUEST);
        }

        $absoluteFilePath = $this->responsesDirPath . '/' . $filePath;
        $response = new BinaryFileResponse($absoluteFilePath);

        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        if ($mimeTypeGuesser->isGuesserSupported()) {
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($absoluteFilePath));
        } else {
            $response->headers->set('Content-Type', 'text/plain');
        }

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filePath
        );

        return $response;
    }

    /**
     * @Route("/assignment-responses/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_STUDENT", message="Access denied!")
     */
    public function removeAssignmentResponse(Request $request, int $id): Response
    {
        try {
            return $this->json(
                $this->assignmentService->removeAssignmentResponse($id)
            );
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/assignments/{id}/similarity-report", methods={"GET"})
     * @Security("is_granted('ROLE_TEACHER') or is_granted('ROLE_STUDENT')", message="Access denied.")
     */
    public function getSimilarityReport(Request $request, int $id): Response
    {
        $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));
        $loggedIn = $this->userService->getUser((int)$token['id']);

        try {
            if (true === AccessValidator::hasTeacherAccess($loggedIn->getRoles())) {
                return $this->json($this->nlpHelper->getTeacherSimilarityScriptOutput(
                    $this->assignmentService->getTeacherNlpInputsByAssignment($id)
                ));
            }

            $nlpStudentInputs = $this->assignmentService->getStudentNlpInputsByAssignment($id, $loggedIn->getId());

            return $this->json(
                $this->nlpHelper->getStudentSimilarityScriptOutput(
                    $nlpStudentInputs['studentInput'],
                    $nlpStudentInputs['otherInputs']
                ));
        } catch (\JsonException $exception) {
            return $this->json('An error has occured. Please try again later.', Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (ServiceException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}