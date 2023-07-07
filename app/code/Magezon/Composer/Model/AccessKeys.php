<?php
namespace Magezon\Composer\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel;
use Magezon\Composer\Api\Data\AccessKeysInterface;
use Magezon\Composer\Model\ResourceModel\AccessKeys as AccessKeysResource;

class AccessKeys extends AbstractModel implements AccessKeysInterface
{

    /**
     * Access Keys constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(AccessKeysResource::class);
    }

    /**
     * Save from collection data
     *
     * @param array $data
     * @return $this
     * @throws AlreadyExistsException
     */
    public function saveCollection(array $data)
    {
        if (isset($data[$this->getId()])) {
            $this->addData($data[$this->getId()]);
            $this->_resource->save($this);
        }
        return $this;
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
     * @return \Magezon\Composer\Api\Data\AccessKeysInterface
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
