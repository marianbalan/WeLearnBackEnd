<?php

namespace App\Service;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class TokenManagerService
{
    public function __construct(
        private JWTEncoderInterface $JWTEncoder,
    ) {
    }

    public function generateAuthToken(User $user, int $exp): string
    {
        return $this->JWTEncoder->encode([
           'exp' => $exp,
           'id' => $user->getId(),
           'username' => $user->getEmail(),
           'roles' => $user->getRoles(),
           'schoolId' => $user->getSchool()->getId(),
           'studyGroupId' => null !== $user->getStudyGroup()
               ? $user->getStudyGroup()->getId() : null,
           'firstName' => $user->getFirstName(),
           'lastName' => $user->getLastName(),
           'pin' => $user->getPin()
        ]);
    }

    public function generateConfirmationToken(User $user): string
    {
        return $this->JWTEncoder->encode([
            'email' => $user->getEmail(),
            'activated' => $user->getActivated()
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function decodeBearerToken(string $bearerToken): array
    {
        return $this->JWTEncoder->decode(\explode(' ', $bearerToken)[1]);
    }

    /**
     * @return array<string, string>
     */
    public function decodeToken(string $token): array
    {
        return $this->JWTEncoder->decode($token);
    }
}