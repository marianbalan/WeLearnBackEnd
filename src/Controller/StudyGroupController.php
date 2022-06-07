<?php

namespace App\Controller;

use App\Dto\Dto\StudyGroupDto;
use App\Service\Exception\ServiceException;
use App\Service\StudyGroupService;
use App\Service\TokenManagerService;
use App\Service\UserService;
use App\Validator\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class StudyGroupController extends AbstractController
{
    public function __construct(
        private StudyGroupService $studyGroupService,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route("/study-group", methods={"POST"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function addStudyGroup(Request $request): Response
    {
        try {
            $studyGroup = $this->serializer->deserialize(
                $request->getContent(),
                StudyGroupDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->studyGroupService->addStudyGroup($studyGroup));
        } catch (MissingConstructorArgumentsException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/study-groups/school/{id}", methods={"GET"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function getStudyGroupsBySchoolId(Request $request, int $id): Response
    {
        try {
            return $this->json($this->studyGroupService->getStudyGroupsBySchoolId($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/study-group/{id}", methods={"PUT"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function updateStudyGroup(Request $request, int $id): Response
    {
        try {
            $studyGroup = $this->serializer->deserialize(
                $request->getContent(),
                StudyGroupDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->studyGroupService->updateStudyGroup($id, $studyGroup));
        } catch (MissingConstructorArgumentsException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/study-group/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function removeStudyGroup(Request $request, int $id): Response
    {
        try {
            return $this->json($this->studyGroupService->removeStudyGroup($id));
        } catch (MissingConstructorArgumentsException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
