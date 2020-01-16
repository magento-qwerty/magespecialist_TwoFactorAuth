<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use MSP\TwoFactorAuth\Model\Provider;
use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $model;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(Provider::class);
    }

    /**
     * Check that trusted devices functionality is disabled
     *
     * @return void
     */
    public function testTrustedDevicesEnabled(): void
    {
        $this->assertFalse($this->model->isTrustedDevicesAllowed());
    }
}
