<?php
namespace Magezon\Composer\Model\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magezon\Composer\Api\Data\AccessKeysInterface;

class AccessKeys extends AbstractModel implements IdentityInterface, AccessKeysInterface
{
    /**
     * Access keys constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magezon\Composer\Model\ResourceModel\AccessKeys::class);
    }

    /**
     * Get Access Key customer id
     *
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set Access Key customer id
     *
     * @param string $customerId
     * @return AccessKeys|void
     */
    public function setCustomerId(string $customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get Access Key Id
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * Set created date
     *
     * @param string $date
     * @return $this
     */
    public function setCreatedAt(string $date): AccessKeys
    {
        $this->setData(self::CREATED_DATE, $date);
        return $this;
    }

    /**
     * Get Access Key crated date
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string) $this->getData(self::CREATED_DATE);
    }

    /**
     * Get Identifiers
     *
     * @return int[]
     */
    public function getIdentities(): array
    {
        return [$this->getid()];
    }

    /**
     * Check key status
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->getData(self::STATUS);
    }

    /**
     * Generate Access Key
     *
     * @param string $access_key
     * @return AccessKeys
     */
    public function setAccessKey(string $access_key)
    {
        return $this->setData(self::ACCESS_KEY, $access_key);
    }
    /**
     * Generate Access Secret
     *
     * @param string $access_secret
     * @return AccessKeys
     */
    public function setAccessSecret(string $access_secret)
    {
        return $this->setData(self::ACCESS_SECRET, $access_secret);
    }

    /**
     * Generate Access Key
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
