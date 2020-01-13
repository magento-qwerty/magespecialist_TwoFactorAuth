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

declare(strict_types=1);

namespace MSP\TwoFactorAuth\Block;

use Magento\Backend\Block\Template;
use Magento\User\Model\User;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\ProviderInterface;

class Configure extends Template
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @param Template\Context $context
     * @param array $data
     * @param TfaInterface $tfa
     */
    public function __construct(Template\Context $context, TfaInterface $tfa, array $data = [])
    {
        parent::__construct($context, $data);
        $this->tfa = $tfa;
    }

    /**
     * Create list of providers for user to choose.
     *
     * @return array
     */
    public function getProvidersList(): array
    {
        $selected = $this->tfa->getForcedProviders();
        $list = [];
        foreach ($this->tfa->getAllEnabledProviders() as $provider) {
            $list[] = [
                'code' => $provider->getCode(),
                'name' => $provider->getName(),
                'icon' => $this->getViewFileUrl($provider->getIcon()),
                'selected' => in_array($provider, $selected, true)
            ];
        }

        return $list;
    }

    /**
     * Get the form's action URL.
     *
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->getUrl('msp_twofactorauth/tfa/configurepost');
    }
}
