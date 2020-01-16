<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Duo;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use MSP\TwoFactorAuth\TestFramework\TestCase\AbstractConfigureBackendController;

/**
 * Test for the DuoSecurity form.
 *
 * @magentoAppArea adminhtml
 */
class AuthTest extends AbstractConfigureBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/msp_twofactorauth/duo/auth';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_GET;

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/integration_key duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/secret_key duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/api_hostname duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/application_key duo_security
     */
    public function testTokenAccess(): void
    {
        parent::testTokenAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/integration_key duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/secret_key duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/api_hostname duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/application_key duo_security
     */
    public function testAclHasAccess()
    {
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/integration_key duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/secret_key duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/api_hostname duo_security
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/duo/application_key duo_security
     */
    public function testAclNoAccess()
    {
        parent::testAclNoAccess();
    }
}
