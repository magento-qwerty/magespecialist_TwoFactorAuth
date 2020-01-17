<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\TestFramework;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\Event\Observer;
use MSP\TwoFactorAuth\Observer\ControllerActionPredispatch as ParentObserver;

/**
 * Observer that allows integration controller tests that are not aware of 2FA to run.
 */
class ControllerActionPredispatch extends ParentObserver
{
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var $controllerAction AbstractAction */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        if (class_exists('Magento\TestFramework\Request')
            && $controllerAction->getRequest() instanceof \Magento\TestFramework\Request
            && !$controllerAction->getRequest()->getParam('tfa_enabled')
        ) {
            //Hack that allows integration controller tests that are not aware of 2FA to run
            return;
        }

        parent::execute($observer);
    }
}
