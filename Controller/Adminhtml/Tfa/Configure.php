<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use MSP\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;
use Magento\Backend\Model\Auth\Session;

/**
 * Configure 2FA for the application.
 */
class Configure extends AbstractAction implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'MSP_TwoFactorAuth::config';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param TfaInterface $tfa
     * @param HtmlAreaTokenVerifier $tokenVerifier
     * @param Session $session
     */
    public function __construct(
        Context $context,
        TfaInterface $tfa,
        HtmlAreaTokenVerifier $tokenVerifier,
        Session $session
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->tokenVerifier = $tokenVerifier;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $user = $this->session->getUser();
        if (!$this->tfa->getUserProviders($user->getId()) && !$this->tokenVerifier->isConfigTokenProvided()) {
            return $this->_redirect('msp_twofactorauth/tfa/requestconfig');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}