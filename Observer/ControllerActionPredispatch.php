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

namespace MSP\TwoFactorAuth\Observer;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\User\Model\User;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\TfaSessionInterface;
use MSP\TwoFactorAuth\Api\UserConfigRequestManagerInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var AbstractAction|null
     */
    private $action;

    public function __construct(
        TfaInterface $tfa,
        Session $session,
        TfaSessionInterface $tfaSession,
        UserConfigRequestManagerInterface $configRequestManager
    ) {
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->session = $session;
        $this->configRequestManager = $configRequestManager;
    }

    /**
     * Get current user
     * @return User|null
     */
    private function getUser()
    {
        return $this->session->getUser();
    }

    /**
     * Redirect user to given URL.
     *
     * @param string $url
     * @return void
     */
    private function redirect(string $url): void
    {
        $this->action->getActionFlag()->set('', Action::FLAG_NO_DISPATCH, true);
        $this->action->getResponse()->setRedirect($this->action->getUrl($url));
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var $controllerAction AbstractAction */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        $this->action = $controllerAction;
        $fullActionName = $controllerAction->getRequest()->getFullActionName();

        if (in_array($fullActionName, $this->tfa->getAllowedUrls())) {
            //Actions that are used for 2FA must remain accessible.
            return;
        }

        $user = $this->getUser();
        if ($user) {
            if ($this->configRequestManager->isConfigurationRequiredFor((string)$user->getId())) {
                //User must configure 2FA first
                $this->redirect('msp_twofactorauth/tfa/requestconfig');
            } else {
                //2FA required
                $accessGranted = $this->tfaSession->isGranted();
                if (!$accessGranted) {
                    $this->redirect('msp_twofactorauth/tfa/index');
                }
            }
        }
    }
}
