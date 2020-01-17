<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_TwoFactorAuth
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\UserConfigManagerInterface;
use MSP\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use MSP\TwoFactorAuth\Controller\Adminhtml\AbstractAction;

class Index extends AbstractAction
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $userConfigRequest;

    /**
     * @param Context $context
     * @param Session $session
     * @param UserConfigManagerInterface $userConfigManager
     * @param TfaInterface $tfa
     * @param UserConfigRequestManagerInterface $userConfigRequestManager
     */
    public function __construct(
        Context $context,
        Session $session,
        UserConfigManagerInterface $userConfigManager,
        TfaInterface $tfa,
        UserConfigRequestManagerInterface $userConfigRequestManager
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->session = $session;
        $this->userConfigManager = $userConfigManager;
        $this->context = $context;
        $this->userConfigRequest = $userConfigRequestManager;
    }

    /**
     * Get current user
     * @return \Magento\User\Model\User|null
     */
    private function getUser()
    {
        return $this->session->getUser();
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $user = $this->getUser();

        if (!$this->tfa->getUserProviders($user->getId())) {
            //If 2FA is not configured - request configuration.
            return $this->_redirect('msp_twofactorauth/tfa/requestconfig');
        }
        $providersToConfigure = $this->tfa->getProvidersToActivate($user->getId());
        if (!empty($providersToConfigure)) {
            //2FA provider not activated - redirect to the provider form.
            return $this->_redirect($providersToConfigure[0]->getConfigureAction());
        }

        $providerCode = '';

        $defaultProviderCode = $this->userConfigManager->getDefaultProvider($user->getId());
        if ($this->tfa->getProviderIsAllowed($user->getId(), $defaultProviderCode)) {
            //If default provider was configured - select it.
            $providerCode = $defaultProviderCode;
        }

        if (!$providerCode) {
            //Select one random provider.
            $providers = $this->tfa->getUserProviders($user->getId());
            if (!empty($providers)) {
                $providerCode = $providers[0]->getCode();
            }
        }

        if (!$providerCode) {
            //Couldn't find provider - perhaps something is not configured properly.
            return $this->_redirect($this->context->getBackendUrl()->getStartupPageUrl());
        }

        if ($provider = $this->tfa->getProvider($providerCode)) {
            //Provider found, user will be challenged.
            return $this->_redirect($provider->getAuthAction());
        }

        throw new LocalizedException(__('Internal error accessing 2FA index page'));
    }
}
