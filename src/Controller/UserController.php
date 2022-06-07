<?php

namespace App\Controller;

use App\Dto\Dto\SchoolDto;
use App\Dto\Dto\UserDto;
use App\Service\Exception\ServiceException;
use App\Service\TokenManagerService;
use App\Service\UserService;
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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    public function __construct(
        private TokenManagerService $tokenManagerService,
        private UserService $userService,
        private SerializerInterface $serializer,
        private DenormalizerInterface $denormalizer,
        private DecoderInterface $decoder,
    ) {
    }

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        try {
            $directorInformation = $this->decoder->decode($request->getContent(), JsonEncoder::FORMAT);

            if (false === \array_key_exists('user', $directorInformation) || false === \array_key_exists('school', $directorInformation)) {
                return $this->json("The information did not reach server!", Response::HTTP_BAD_REQUEST);
            }

            $school = $this->denormalizer->denormalize(
                $directorInformation['school'],
                SchoolDto::class
            );

            $user = $this->denormalizer->denormalize(
                $directorInformation['user'],
                UserDto::class
            );

            return $this->json($this->userService->register($user, $school));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_CONFLICT);
        }
    }

    /**
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        try {
            $loginData = $this->decoder->decode($request->getContent(), JsonEncoder::FORMAT);

            if (false === \array_key_exists('email', $loginData) || false === \array_key_exists('password', $loginData)) {
                return $this->json("Email and password required!", Response::HTTP_BAD_REQUEST);
            }

            return $this->json($this->userService->login($loginData['email'], $loginData['password']));
        } catch (ServiceException | NotEncodableValueException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/user", methods={"POST"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function addUser(Request $request): Response
    {
        try {
            $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));
            $reporter = $this->userService->getUser((int) $token['id']);

            $userToAdd = $this->serializer->deserialize(
                $request->getContent(),
                UserDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->userService->addUser($userToAdd, $reporter));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/users/school/{id}/role/{role}", methods={"GET"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function getUsersBySchoolAndRole(Request $request, int $id, string $role): Response
    {
        try {
            $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));

            if ($id !== (int) $token['schoolId']) {
                return $this->json('Access denied!', Response::HTTP_UNAUTHORIZED);
            }

            return $this->json($this->userService->getBySchoolAndRole($role, $id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/users/school/{id}/non-class-master-teachers", methods={"GET"})
     * @IsGranted("ROLE_DIRECTOR", message="Access denied!")
     */
    public function getNonClassMasterTeachers(Request $request, int $id): Response
    {
        try {
            $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));

            if ($id !== (int) $token['schoolId']) {
                return $this->json('Access denied!', Response::HTTP_UNAUTHORIZED);
            }

            return $this->json($this->userService->getNonClassMasterTeachersBySchoolId($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function updateUser(Request $request, int $id): Response
    {
        try {
            $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));
            $loggedIn = $this->userService->getUser((int) $token['id']);

            $userDto = $this->serializer->deserialize(
                $request->getContent(),
                UserDto::class,
                JsonEncoder::FORMAT
            );

            return $this->json($this->userService->updateUser($id, $userDto, $loggedIn));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/user/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function removeUser(Request $request, int $id): Response
    {
        try {
            $token = $this->tokenManagerService->decodeBearerToken($request->headers->get('Authorization'));
            $loggedInUser = $this->userService->getUser((int) $token['id']);

            return $this->json($this->userService->removeUser($id, $loggedInUser));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException | ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/users/study-group/{id}/role/{role}", methods={"GET"})
     * @Security("is_granted('ROLE_DIRECTOR') or is_granted('ROLE_CLASS_MASTER')", message="Access denied.")
     */
    public function getUsersByStudyGroupAndRole(Request $request, int $id, string $role): Response
    {
        try {
            return $this->json($this->userService->getByStudyGroupAndRole($role, $id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/users/{id}", methods={"GET"})
     * @IsGranted("ROLE_USER", message="Access denied!")
     */
    public function getUserById(Request $request, int $id): Response
    {
        try {
            return $this->json($this->userService->getUserAsViewModel($id));
        } catch (UnauthorizedHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/activate-account", methods={"POST"})
     */
    public function activateAccount(Request $request): Response
    {
        try {
            return $this->json($this->userService->activateAccount($request->getContent()));
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/set-password", methods={"POST"})
     */
    public function setPassword(Request $request): Response
    {
        try {
            $data = $this->decoder->decode($request->getContent(), JsonEncoder::FORMAT);

            if (false === \array_key_exists('token', $data) || false === \array_key_exists('password', $data)) {
                return $this->json('Invalid files!', Response::HTTP_BAD_REQUEST);
            }

            return $this->json($this->userService->setPassword($data['token'], $data['password']));
        } catch (MissingConstructorArgumentsException | NotEncodableValueException $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (ServiceException $e) {
            return $this->json($e->getMessage(), Response::HTTP_CONFLICT);
        }
    }
}
