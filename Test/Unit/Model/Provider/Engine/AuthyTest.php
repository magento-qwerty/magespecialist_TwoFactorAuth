<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Test\Unit\Model\Provider\Engine;

use MSP\TwoFactorAuth\Model\Provider\Engine\Authy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class AuthyTest extends TestCase
{
    /**
     * @var Authy
     */
    private $model;

    /**
     * @var MockObject|Authy\Service
     */
    private $serviceMock;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->serviceMock = $this->getMockBuilder(Authy\Service::class)->disableOriginalConstructor()->getMock();

        $this->model = $objectManager->getObject(Authy::class, ['service' => $this->serviceMock]);
    }

    /**
     * Check that trusted devices functionality is disabled.
     *
     * @return void
     */
    public function testIsTrustedDevicesAllowed(): void
    {
        $this->assertFalse($this->model->isTrustedDevicesAllowed());
    }

    /**
     * Enabled test dataset.
     *
     * @return array
     */
    public function getIsEnabledTestDataSet(): array
    {
        return [
            'api key present' => [
                'api-key',
                true
            ],
            'api key not configured' => [
                null,
                false
            ]
        ];
    }

    /**
     * Check that the provider is available based on configuration.
     *
     * @param string|null $apiKey
     * @param bool $expected
     * @return void
     * @dataProvider getIsEnabledTestDataSet
     */
    public function testIsEnabled(?string $apiKey, bool $expected): void
    {
        $this->serviceMock->method('getApiKey')->willReturn($apiKey);

        $this->assertEquals($expected, $this->model->isEnabled());
    }
}