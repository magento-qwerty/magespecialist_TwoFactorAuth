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
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use MSP\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use MSP\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

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
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param HtmlAreaTokenVerifier $tokenVerifier
     * @param TfaInterface $tfa
     * @param Session $session
     */
    public function __construct(
        Context $context,
        UserConfigRequestManagerInterface $configRequestManager,
        HtmlAreaTokenVerifier $tokenVerifier,
        TfaInterface $tfa,
        Session $session
    ) {
        parent::__construct($context);
        $this->configRequestManager = $configRequestManager;
        $this->tokenVerifier = $tokenVerifier;
        $this->tfa = $tfa;
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
        if ($this->tokenVerifier->isConfigTokenProvided()) {
            if (!$this->tfa->getForcedProviders()) {
                return $this->_redirect('msp_twofactorauth/tfa/configure');
            } else {
                return $this->_redirect('msp_twofactorauth/tfa/index');
            }
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