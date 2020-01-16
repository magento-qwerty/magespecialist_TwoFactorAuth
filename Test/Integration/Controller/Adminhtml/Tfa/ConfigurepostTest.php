<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Test\Integration\Controller\Adminhtml\Tfa;

use Magento\TestFramework\Helper\Bootstrap;
use MSP\TwoFactorAuth\TestFramework\TestCase\AbstractBackendController;
use MSP\TwoFactorAuth\Api\ProviderInterface;
use MSP\TwoFactorAuth\Api\TfaInterface;
use MSP\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use MSP\TwoFactorAuth\Model\Provider\Engine\Google;

/**
 * Testing the controller for the page that allows to configure 2FA providers.
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class ConfigurepostTest extends AbstractBackendController
{
    /**
     * @inheritDoc
     */
    protected $uri = 'backend/msp_twofactorauth/tfa/configurepost';

    /**
     * @inheritDoc
     */
    protected $resource = 'MSP_TwoFactorAuth::config';

    /**
     * @inheritDoc
     */
    protected $httpMethod = Request::METHOD_POST;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var TfaInterface
     */
    private $tfa;

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
        $this->tfa = Bootstrap::getObjectManager()->get(TfaInterface::class);
    }

    /**
     * Verify that 2FA providers are updated when a user submits the form.
     *
     * @return void
     */
    public function testUpdated(): void
    {
        $providerCode = Google::CODE;
        $this->getRequest()->setMethod(Request::METHOD_POST);
        $this->getRequest()
            ->setParam('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        $this->getRequest()->setPostValue([
            'tfa_selected' => [$providerCode => 'on']
        ]);
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('admin'));
        $this->assertNotEmpty($providers = $this->tfa->getForcedProviders());
        /** @var ProviderInterface $provider */
        $provider = array_pop($providers);
        $this->assertEquals($providerCode, $provider->getCode());
    }

    /**
     * Verify that token is required to proceed.
     *
     * @return void
     */
    public function testWithoutToken(): void
    {
        $this->getRequest()->setMethod(Request::METHOD_POST);
        $this->getRequest()->setPostValue([
            'tfa_selected' => [Google::CODE => 'on']
        ]);
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('login'));
        $this->assertEmpty($this->tfa->getForcedProviders());
    }

    /**
     * Verify that token is required to proceed even if providers area already configured.
     *
     * @return void
     * @magentoConfigFixture default/msp_securitysuite_twofactorauth/general/force_providers google
     */
    public function testConfiguredWithoutToken(): void
    {
        $this->getRequest()->setMethod(Request::METHOD_POST);
        $this->getRequest()->setPostValue([
            'tfa_selected' => ['nonExisting' => 'on']
        ]);
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('login'));
    }

    /**
     * Verify that 2FA providers are validated
     *
     * @return void
     */
    public function testValidated(): void
    {
        $this->getRequest()->setMethod(Request::METHOD_POST);
        $this->getRequest()
            ->setQueryValue('tfat', $this->tokenManager->issueFor((string)$this->_session->getUser()->getId()));
        $this->getRequest()->setPostValue([
            'tfa_selected' => ['nonExisting' => 'on']
        ]);
        $this->dispatch($this->uri);
        $this->assertRedirect($this->stringContains('configure'));
        $this->assertEmpty($this->tfa->getForcedProviders());
        $this->assertSessionMessages($this->contains(__('Please select valid providers.')->render()));
    }

    /**
     * @inheritDoc
     */
    public function testAclHasAccess()
    {
        $this->markTestSkipped('Subsequently tested with the tests above.');
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
