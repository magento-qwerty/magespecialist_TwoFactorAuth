<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Api;

/**
 * Manages tokens issued to users to authorize them to configure 2FA.
 */
interface UserConfigTokenManagerInterface
{
    /**
     * Issue token for the user.
     *
     * @param string $userId
     * @return string
     */
    public function issueFor(string $userId): string;

    /**
     * Is given token valid for given user?
     *
     * @param string $userId
     * @param string $token
     * @return bool
     */
    public function isValidFor(string $userId, string $token): bool;
}