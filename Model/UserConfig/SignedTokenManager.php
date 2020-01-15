<?php
declare(strict_types=1);

namespace MSP\TwoFactorAuth\Model\UserConfig;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MSP\TwoFactorAuth\Api\UserConfigTokenManagerInterface;

/**
 * @inheritDoc
 */
class SignedTokenManager implements UserConfigTokenManagerInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param EncryptorInterface $encryptor
     * @param Json $json
     * @param DateTime $dateTime
     */
    public function __construct(EncryptorInterface $encryptor, Json $json, DateTime $dateTime)
    {
        $this->encryptor = $encryptor;
        $this->json = $json;
        $this->dateTime = $dateTime;
    }

    /**
     * @inheritDoc
     */
    public function issueFor(string $userId): string
    {
        $data = ['user_id' => $userId, 'tfa_configuration' => true, 'iss' => $this->dateTime->timestamp()];
        $encodedData = $this->json->serialize($data);
        $signature = base64_encode($this->encryptor->hash($encodedData));

        return base64_encode($encodedData .'.' .$signature);
    }

    /**
     * @inheritDoc
     */
    public function isValidFor(string $userId, string $token): bool
    {
        $isValid = false;
        [$encodedData, $signatureProvided] = explode('.', base64_decode($token));
        try {
            $data = $this->json->unserialize($encodedData);
            if (array_key_exists('user_id', $data)
                && array_key_exists('tfa_configuration', $data)
                && array_key_exists('iss', $data)
                && $data['user_id'] === $userId
                && $data['tfa_configuration']
                && ($this->dateTime->timestamp() - (int)$data['iss']) < 3600
                && Security::compareStrings(base64_encode($this->encryptor->hash($encodedData)), $signatureProvided)
            ) {
                $isValid = true;
            }
        } catch (\Throwable $exception) {
            $isValid = false;
        }

        return $isValid;
    }
}