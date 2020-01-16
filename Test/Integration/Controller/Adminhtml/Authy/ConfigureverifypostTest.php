<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Authy;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use MSP\TwoFactorAuth\TestFramework\TestCase\AbstractConfigureBackendController;

/**
 * Test for the configure authy 2FA form processor.
 *
 * @magentoAppArea adminhtml
 */
class ConfigureverifypostTest extends AbstractConfigureBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/msp_twofactorauth/authy/configurepost';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_POST;

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/authy/api_key some-key
     */
    public function testTokenAccess(): void
    {
        parent::testTokenAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/authy/api_key some-key
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers authy
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/authy/api_key some-key
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
