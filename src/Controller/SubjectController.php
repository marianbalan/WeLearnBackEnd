<?php

namespace App\Controller;

use App\Dto\Dto\SubjectDto;
use App\Service\Exception\ServiceException;
use App\Service\SubjectService;
use App\Service\TokenManagerService;
use App\Service\UserService;
use App\Validator\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class SubjectController extends AbstractController
{
    public function __construct(
        private SubjectService $subjectService,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route("/subject", methods={"POST"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function addSubject(Request $request): Response
    {
        try {
            $subjectToAdd = $this->serializer->deserialize(
                $request->getContent(),
                SubjectDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->subjectService->addSubject($subjectToAdd));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/subjects/teacher/{id}", methods={"GET"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function getSubjectsByTeacherId(Request $request, int $id): Response
    {
        try {
            return $this->json($this->subjectService->getByTeacher($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/subjects/study-group/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function getSubjectsByStudyGroupId(Request $request, int $id): Response
    {
        try {
            return $this->json($this->subjectService->getByStudyGroup($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/subjects/school/{id}", methods={"GET"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function getSubjectsBySchoolId(Request $request, int $id): Response
    {
        try {
            return $this->json($this->subjectService->getBySchool($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/subject/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function removeSubject(Request $request, int $id): Response
    {
        try {
            return $this->json($this->subjectService->removeSubject($id));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/subject/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function updateSubject(Request $request, int $id): Response
    {
        try {
            $subject = $this->serializer->deserialize(
                $request->getContent(),
                SubjectDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->subjectService->updateSubject($id, $subject));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/subjects/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER') or is_granted('ROLE_TEACHER')", message="Access denied.")
     */
    public function getSubject(Request $request, int $id): Response
    {
        try {
            return $this->json($this->subjectService->getSubject($id));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
