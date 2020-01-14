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

use Magento\Backend\App\Action\Context;
use MSP\TwoFactorAuth\Controller\Adminhtml\AbstractAction;

/**
 * @deprecated Trusted Devices functionality was removed.
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Revoke extends AbstractAction
{
    /**
     * @param Context $context
     * @param \MSP\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager
     */
    public function __construct(
        Context $context,
        \MSP\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('MSP_TwoFactorAuth::tfa');
    }
}
