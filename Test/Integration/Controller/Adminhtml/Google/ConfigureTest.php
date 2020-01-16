<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Google;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use MSP\TwoFactorAuth\TestFramework\TestCase\AbstractConfigureBackendController;

/**
 * Test for the configure google 2FA form page.
 *
 * @magentoAppArea adminhtml
 */
class ConfigureTest extends AbstractConfigureBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/msp_twofactorauth/google/configure';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_GET;

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     */
    public function testTokenAccess(): void
    {
        parent::testTokenAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
