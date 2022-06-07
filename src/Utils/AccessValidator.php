<?php

namespace App\Utils;

class AccessValidator
{
    /**
     * @param string[] $reporterRoles
     */
    public static function hasAddUserAccess(array $reporterRoles, string $reportedRole): bool
    {
        return match ($reportedRole) {
            UserRole::STUDENT =>
                \in_array(UserRole::DIRECTOR, $reporterRoles) ||
                \in_array(UserRole::CLASS_MASTER, $reporterRoles),
            UserRole::TEACHER => \in_array(UserRole::DIRECTOR, $reporterRoles),
            default => false
        };
    }

    /**
     * @param string[] $reporterRoles
     * @param string[] $reportedRoles
     */
    public static function hasAddStudyGroupAccess(array $reporterRoles, array $reportedRoles): bool
    {
        if (false === \in_array(UserRole::DIRECTOR, $reporterRoles, true)) {
            return false;
        }

        return \in_array(UserRole::TEACHER, $reportedRoles, true);
    }

    /**
     * @param string[] $reporterRoles
     * @param string[] $reportedRoles
     */
    public static function hasAddSituationAccess(array $reporterRoles, array $reportedRoles): bool
    {
        return (\in_array(UserRole::DIRECTOR, $reporterRoles, true)
                || \in_array(UserRole::TEACHER, $reporterRoles, true)) &&
            \in_array(UserRole::STUDENT, $reportedRoles, true);
    }

    /**
     * @param string[] $reporterRoles
     */
    public static function hasAddSubjectAccess(array $reporterRoles): bool
    {
        return (\in_array(UserRole::DIRECTOR, $reporterRoles, true)
            || \in_array(UserRole::CLASS_MASTER, $reporterRoles, true));
    }

    /**
     * @param string[] $roles
     */
    public static function hasTeacherAccess(array $roles): bool
    {
        return \in_array(UserRole::TEACHER, $roles, true);
    }

    /**
     * @param string[] $roles
     */
    public static function hasDirectorAccess(array $roles): bool
    {
        return \in_array(UserRole::DIRECTOR, $roles, true);
    }

    /**
     * @param string[] $roles
     */
    public static function hasClassMasterAccess(array $roles): bool
    {
        return \in_array(UserRole::CLASS_MASTER, $roles, true);
    }

    /**
     * @param string[] $roles
     */
    public static function hasStudentAccess(array $roles): bool
    {
        return \in_array(UserRole::STUDENT, $roles, true);
    }

    /**
     * @param string[] $roles
     */
    public static function hasUserAccess(array $roles): bool
    {
        return \in_array(UserRole::USER, $roles, true);
    }

    /**
     * @param string[] $reporterRoles
     * @param string[] $reportedRoles
     */
    public static function hasRemoveUserAccess(array $reporterRoles, array $reportedRoles): bool
    {
        return \in_array(UserRole::DIRECTOR, $reporterRoles) ||
            (\in_array(UserRole::CLASS_MASTER, $reporterRoles) && \in_array(UserRole::STUDENT, $reportedRoles, true));
    }
}