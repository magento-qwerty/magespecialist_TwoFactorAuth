<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Api;

use Magento\Framework\Exception\AuthorizationException;
use Magento\User\Model\User;
use MSP\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;

/**
 * Manages configuration requests for users.
 */
interface UserConfigRequestManagerInterface
{
    /**
     * Is user required to configure 2FA?
     *
     * @param string $userId
     * @return bool
     */
    public function isConfigurationRequiredFor(string $userId): bool;

    /**
     * Request configurations from the user.
     *
     * @param User $user
     * @return void
     * @throws AuthorizationException When user is not allowed to configure 2FA.
     * @throws NotificationExceptionInterface When failed to send the message.
     */
    public function sendConfigRequestTo(User $user): void;
}