<?php
namespace Magezon\Composer\Api\Data;

interface AccessKeysInterface
{
    public const IDENTIFIER = "access_id";
    public const KEY_NAME="name";
    public const ACCESS_KEY = "access_key";
    public const ACCESS_SECRET = "access_secret";
    public const CUSTOMER_ID = "entity_id";
    public const CREATED_DATE = "created_at";
    public const MODIFIED_DATE = "modified_at";
    public const EXPIRE_DATE = "expire_at";
    public const STATUS = "status";

    /**
     * Get Access Key Id
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Set ID
     *
     * @param string|null $id
     * @return $this
     */
    public function setId(?string $id);

    /**
     * Get customer ID
     *
     * @return string|null
     */
    public function getCustomerId(): ?string;

    /**
     * Set customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId(string $customerId);
    /**
     * Generate Access Key
     *
     * @param string $access_key
     * @return $this
     */
    public function setAccessKey(string $access_key);
    /**
     * Generate Access Secret
     *
     * @param string $access_secret
     * @return $this
     */
    public function setAccessSecret(string $access_secret);

    /**
     * Generate Access Key
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status);
}
