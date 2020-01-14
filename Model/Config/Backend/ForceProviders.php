<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

/**
 * Managing "Force Providers" config value.
 */
class ForceProviders extends Value
{
    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!$value) {
            throw new ValidatorException(__('You have to select at least one Two-Factor Authorization provider'));
        }

        return parent::beforeSave();
    }
}
