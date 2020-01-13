<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Model\Exception;

use MSP\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;

/**
 * @inheritDoc
 */
class NotificationException extends \RuntimeException implements NotificationExceptionInterface
{

}