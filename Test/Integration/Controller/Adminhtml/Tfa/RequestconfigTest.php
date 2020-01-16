<?php
declare(strict_types=1);


namespace MSP\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Tfa;

use Magento\TestFramework\Helper\Bootstrap;
use MSP\TwoFactorAuth\TestFramework\TestCase\AbstractBackendController;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use MSP\TwoFactorAuth\Model\Provider\Engine\Google;

/**
 * Testing the controller for the page that requests 2FA config from users.
 *
 * @magentoAppArea adminhtml
 */
class RequestconfigTest extends AbstractBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/msp_twofactorauth/tfa/requestconfig';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tfa = Bootstrap::getObjectManager()->get(TfaInterface::class);
        $this->tokenManager = Bootstrap::getObjectManager()->get(UserConfigTokenManagerInterface::class);
    }

    /**
     * Verify that 2FA config request is display for users when 2FA is not configured for the app.
     *
     * @return void
     */
    public function testAppConfigRequested(): void
    {
        $this->dispatch($this->uri);
        $this->assertRegExp('/You need to configure Two\-Factor Authorization/', $this->getResponse()->getBody());
    }

    /**
     * Verify that 2FA config request is display for users when 2FA is not configured for the user.
     *
     * @return void
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     */
    public function testUserConfigRequested(): void
    {
        $this->dispatch($this->uri);
        $this->assertRegExp('/You need to configure Two\-Factor Authorization/', $this->getResponse()->getBody());
    }


    /**
     * Verify that 2FA config is not requested when 2FA is configured.
     *
     * @return void
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     */
    public function testNotRequested(): void
    {
        $this->tfa->getProvider(Google::CODE)->activate($this->_session->getUser()->getId());
        $this->dispatch($this->uri);
    }

    /**
     * Verify that users with valid tokens get redirected to the app 2FA config page.
     *
     * @return void
     */
    public function testRedirectToAppConfig(): void
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('tfa/configure'));
    }

    /**
     * Verify that users with valid tokens get redirected to the user 2FA config page.
     *
     * @return void
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     */
    public function testRedirectToUserConfig(): void
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('tfa/index'));
    }
}
