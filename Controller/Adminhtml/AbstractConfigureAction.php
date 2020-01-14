<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use MSP\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Base action class for controllers related to 2FA provider configuration.
 */
abstract class AbstractConfigureAction extends AbstractAction
{
    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @param Context $context
     * @param HtmlAreaTokenVerifier $tokenVerifier
     */
    public function __construct(Context $context, HtmlAreaTokenVerifier $tokenVerifier)
    {
        parent::__construct($context);
        $this->tokenVerifier = $tokenVerifier;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $isAllowed = parent::_isAllowed();
        if ($isAllowed) {
            $isAllowed = $this->tokenVerifier->isConfigTokenProvided();
        }

        return $isAllowed;
    }
}