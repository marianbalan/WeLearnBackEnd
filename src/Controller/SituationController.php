<?php

namespace App\Controller;

use App\Dto\Dto\GradeDto;
use App\Dto\Dto\NonAttendanceDto;
use App\Dto\Mapper\StudyGroupMapper;
use App\Dto\Mapper\UserMapper;
use App\Service\Exception\ServiceException;
use App\Service\GradeService;
use App\Service\NonAttendanceService;
use App\Service\SituationService;
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
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class SituationController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private GradeService $gradeService,
        private NonAttendanceService $nonAttendanceService,
        private SituationService $situationService,
        private SerializerInterface $serializer,
        private DecoderInterface $decoder,
    ) {
    }

    /**
     * @Route("/grade", methods={"POST"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function addGrade(Request $request): Response
    {
        try {
            $gradeToAdd = $this->serializer->deserialize(
                $request->getContent(),
                GradeDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->gradeService->addGrade($gradeToAdd));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/non-attendance", methods={"POST"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function addNonAttendance(Request $request): Response
    {
        try {
            $nonAttendanceToAdd = $this->serializer->deserialize(
                $request->getContent(),
                NonAttendanceDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->nonAttendanceService->addNonAttendance($nonAttendanceToAdd));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/grades/user/{studentId}/subject/{subjectId}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER') or is_granted('ROLE_TEACHER')", message="Access denied.")
     */
    public function getGradesByStudentIdAndSubjectId(Request $request, int $studentId, int $subjectId): Response
    {
        try {
            return $this->json(
                $this->gradeService->getGradesByStudentIdAndSubjectId($studentId, $subjectId)
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/non-attendances/user/{studentId}/subject/{subjectId}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER') or is_granted('ROLE_TEACHER')", message="Access denied.")
     */
    public function getNonAttendancesByStudentIdAndSubjectId(Request $request, int $studentId, int $subjectId): Response
    {
        try {
            return $this->json(
                $this->nonAttendanceService->getNonAttendancesByStudentIdAndSubjectId($studentId, $subjectId)
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/situation/user/{userId}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER') or is_granted('ROLE_STUDENT')", message="Access denied.")
     */
    public function getSituationByUser(Request $request, int $userId): Response
    {
        try {
            return $this->json(
                $this->situationService->getStudentSituationByStudent($userId)
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/situation/subject/{subjectId}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_TEACHER')", message="Access denied.")
     */
    public function getSituationBySubject(Request $request, int $subjectId): Response
    {
        try {
            return $this->json(
                $this->situationService->getSubjectSituationBySubject($subjectId)
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/non-attendance/{id}", methods={"PATCH"})
     * @Security("is_granted('ROLE_CLASS_MASTER') or is_granted('ROLE_TEACHER')", message="Access denied.")
     */
    public function updateNonAttendancePresence(Request $request, int $id): Response
    {
        try {
            $data = $this->decoder->decode(
                $request->getContent(),
                JsonEncoder::FORMAT
            );

            if (false === \array_key_exists('motivated', $data)) {
                return $this->json('Missing files!', Response::HTTP_BAD_REQUEST);
            }

            return $this->json(
                $this->nonAttendanceService->updateNonAttendanceMotivation(
                    $id,
                    (bool) $data['motivated'],
                )
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/grades/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function removeGrade(Request $request, int $id): Response
    {
        try {
            return $this->json($this->gradeService->removeGrade($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/grades/{id}", methods={"PATCH"})
     * @IsGranted("ROLE_TEACHER", message="Access denied!")
     */
    public function updateGradeMark(Request $request, int $id): Response
    {
        try {
            $data = $this->decoder->decode(
                $request->getContent(),
                JsonEncoder::FORMAT
            );

            if (false === \array_key_exists('grade', $data)) {
                return $this->json('Missing files!', Response::HTTP_BAD_REQUEST);
            }

            return $this->json($this->gradeService->editGradeMark($id, $data['grade']));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException | ValidationException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/situation/user/{id}/pdf", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER') or is_granted('ROLE_STUDENT')", message="Access denied.")
     */
    public function exportGradeSituationPdf(Request $request, int $id): Response
    {
        try {
            $student = $this->userService->getUser($id);
            $studentModel = UserMapper::userToUserViewModel($student)
                ->setStudyGroup(StudyGroupMapper::studyGroupToStudyGroupViewModel($student->getStudyGroup()));
            $schoolModel = $student->getSchool();
            $situationModel = $this->situationService->getStudentSituationByStudent($id);

            $html = $this->renderView('base.html.twig', [
                'student' => $studentModel,
                'school' => $schoolModel,
                'situation' => $situationModel
            ]);

            return new Response(
                $html,
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/html',
                ]
            );
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
