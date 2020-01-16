<?php
declare(strict_types=1);


namespace MSP\TwoFactoryAuth\Test\Integration\Controller\Adminhtml\Tfa;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use MSP\TwoFactorAuth\Api\UserConfigTokenManagerInterface;

/**
 * Testing the controller for the page that presents forced providers list to users.
 *
 * @magentoAppArea adminhtml
 */
class ConfigureTest extends AbstractBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/msp_twofactorauth/tfa/configure';

    /**
     * @inheritDoc
     */
    protected $resource = 'MSP_TwoFactorAuth::config';

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @inheritDoc
     */
    protected $expectedNoAccessResponseCode = 302;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tokenManager = Bootstrap::getObjectManager()->get(UserConfigTokenManagerInterface::class);
    }

    /**
     * Verify that 2FA providers form is shown to users when 2FA for the app is not configured and token is present.
     *
     * @return void
     */
    public function testList(): void
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        $this->dispatch($this->uri);
        $this->assertRegExp('/google/', $this->getResponse()->getBody());
    }

    /**
     * Verify that 2FA config request is displayed for users when 2FA is not configured for the user.
     *
     * @return void
     */
    public function testWithoutToken(): void
    {
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('requestconfig'));
    }

    /**
     * @inheritDoc
     */
    public function testAclHasAccess()
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     */
    public function testAclNoAccess()
    {
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        parent::testAclNoAccess();
        $this->assertRedirect($this->stringContains('login'));
    }
}
