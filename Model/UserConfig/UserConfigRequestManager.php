<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Model\UserConfig;

use Magento\Framework\Exception\AuthorizationException;
use Magento\User\Model\User;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use MSP\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use MSP\TwoFactorAuth\Api\UserNotifierInterface;
use Magento\Framework\Authorization\PolicyInterface as Authorization;

/**
 * @inheritDoc
 */
class UserConfigRequestManager implements UserConfigRequestManagerInterface
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserNotifierInterface
     */
    private $notifier;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var Authorization
     */
    private $auth;

    /**
     * @param TfaInterface $tfa
     * @param UserNotifierInterface $notifier
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param Authorization $auth
     */
    public function __construct(
        TfaInterface $tfa,
        UserNotifierInterface $notifier,
        UserConfigTokenManagerInterface $tokenManager,
        Authorization $auth
    ) {
        $this->tfa = $tfa;
        $this->notifier = $notifier;
        $this->tokenManager = $tokenManager;
        $this->auth = $auth;
    }

    /**
     * @inheritDoc
     */
    public function isConfigurationRequiredFor(string $userId): bool
    {
        return empty($this->tfa->getUserProviders($userId)) || !empty($this->tfa->getProvidersToActivate($userId));
    }

    /**
     * @inheritDoc
     */
    public function sendConfigRequestTo(User $user): void
    {
        $userId = (string)$user->getId();
        if (empty($this->tfa->getUserProviders($userId))) {
            //Application level configuration is required.
            if (!$this->auth->isAllowed($user->getAclRole(), 'MSP_TwoFactorAuth::config')) {
                throw new AuthorizationException(__('User is not authorized to edit 2FA configuration'));
            }
            $this->notifier->sendAppConfigRequestMessage($user, $this->tokenManager->issueFor($userId));
        } else {
            //Personal provider config required.
            $this->notifier->sendUserConfigRequestMessage($user, $this->tokenManager->issueFor($userId));
        }
    }
}