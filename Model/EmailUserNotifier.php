<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Email\Model\BackendTemplate;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use MSP\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
use MSP\TwoFactorAuth\Api\UserNotifierInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use MSP\TwoFactorAuth\Model\Exception\NotificationException;
use Psr\Log\LoggerInterface;

/**
 * @inheritDoc
 */
class EmailUserNotifier implements UserNotifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $urlHelper;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlHelper
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlHelper,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlHelper = $urlHelper;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Generate URL for user to configure 2FA.
     *
     * @param string $token
     * @return string
     */
    private function generateConfigUrl(string $token): string
    {
        $url = $this->scopeConfig->getValue('msp_securitysuite_twofactorauth/general/user_config_url');
        if (!$url) {
            $url = 'admin/?tfat={{tfa_token}}';
        }
        $url = str_replace('{{tfa_token}}', $token, $url);

        return $this->urlHelper->getUrl($url);
    }

    /**
     * Send configuration related message to the admin user.
     *
     * @param User $user
     * @param string $token
     * @param string $emailTemplateId
     * @return void
     * @throws NotificationExceptionInterface
     */
    private function sendConfigRequired(User $user, string $token, string $emailTemplateId): void
    {
        $url = $this->generateConfigUrl($token);

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplateId)
                ->setTemplateModel(BackendTemplate::class)
                ->setTemplateOptions([
                    'area' => 'adminhtml',
                    'store' => 0
                ])
                ->setTemplateVars(
                    [
                        'username' => $user->getFirstName() . ' ' . $user->getLastName(),
                        'url' => $url,
                        'store_name' => $this->storeManager->getStore()->getFrontendName()
                    ]
                )
                ->setFrom(
                    $this->scopeConfig->getValue('admin/emails/forgot_email_identity')
                )
                ->addTo($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName())
                ->getTransport();
            $transport->sendMessage();
        } catch (\Throwable $exception) {
            $this->logger->critical($exception);
            throw new NotificationException('Failed to send 2FA E-mail to a user', 0, $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function sendUserConfigRequestMessage(User $user, string $token): void
    {
        $this->sendConfigRequired($user, $token, 'msp_twofactorauth_admin_user_config_required');
    }

    /**
     * @inheritDoc
     */
    public function sendAppConfigRequestMessage(User $user, string $token): void
    {
        $this->sendConfigRequired($user, $token, 'msp_twofactorauth_admin_app_config_required');
    }
}