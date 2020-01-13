<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AuthorizationException;
use MSP\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
use MSP\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use MSP\TwoFactorAuth\Controller\Adminhtml\AbstractAction;

/**
 * Request 2FA config from the user.
 */
class Requestconfig extends AbstractAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        UserConfigRequestManagerInterface $configRequestManager,
        Session $session
    ) {
        parent::__construct($context);
        $this->configRequestManager = $configRequestManager;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $user = $this->session->getUser();
        if (!$this->configRequestManager->isConfigurationRequiredFor((string)$user->getId())) {
            throw new AuthorizationException(__('2FA is already configured for the user.'));
        }

        try {
            $this->configRequestManager->sendConfigRequestTo($user);
        } catch (AuthorizationException $exception) {
            $this->messageManager->addErrorMessage(
                'Please ask an administrator with sufficient access to configure 2FA first'
            );
        } catch (NotificationExceptionInterface $exception) {
            $this->messageManager->addErrorMessage('Failed to send the message. Please contact the administrator');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}