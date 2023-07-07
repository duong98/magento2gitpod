<?php
/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

namespace Magezon\Composer\Api\Data;

interface PackagesInterface
{
    public const ID                   = 'id';
    public const CREATED_AT           = 'created_at';
    public const STATUS               = 'status';
    public const PRODUCT_ID           = 'product_id';
    public const NAME                 = 'name';
    public const DESCRIPTION          = 'description';
    public const REPOSITORY_URL       = 'repository_url';
    public const REPOSITORY_OPTIONS   = 'repository_options';
    public const PACKAGE_JSON         = 'package_json';
    public const VERSION              = 'version';
    public const BUNDLED_PACKAGE      = 'bundled_package';
    public const DEFAULT              = 'default';
    public const CUSTOMER_ID          = 'customer_id';

    public const ORDER_ID             = 'order_id';
    public const PACKAGE_ID           = 'package_id';
    public const LAST_ALLOWED_VERSION = 'last_allowed_version';
    public const LAST_ALLOWED_DATE    = 'last_allowed_date';
    public const VERSION_ID           = 'version_id';
    public const REMOTE_IP            = 'remote_ip';
    public const FILE                 = 'file';

    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get created date
     *
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * Get status
     *
     * @return mixed
     */
    public function getStatus();

    /**
     * Get Product Id
     *
     * @return mixed
     */
    public function getProductId();

    /**
     * Get name
     *
     * @return mixed
     */
    public function getName();

    /**
     * Get Description
     *
     * @return mixed
     */
    public function getDescription();

    /**
     * Get Package Repository Url
     *
     * @return mixed
     */
    public function getRepositoryUrl();

    /**
     * Get Package Repository Option
     *
     * @return mixed
     */
    public function getRepositoryOption();

    /**
     * Get Package generated satis json
     *
     * @return mixed
     */
    public function getPackageJson();

    /**
     * Get package version
     *
     * @return mixed
     */
    public function getVersion();

    /**
     * Get Package satis bundle options
     *
     * @return mixed
     */
    public function getBundledPackage();

    /**
     * Get package default
     *
     * @return mixed
     */
    public function getDefault();

    /**
     * Get packege customer Id
     *
     * @return mixed
     */
    public function getCustomerId();

    /**
     * Get order id
     *
     * @return mixed
     */
    public function getOrderId();

    /**
     * Get package id
     *
     * @return mixed
     */
    public function getPackageId();

    /**
     * Get package last allow version
     *
     * @return mixed
     */
    public function getLastAllowedVersion();

    /**
     * Get package last allow date
     *
     * @return mixed
     */
    public function getLastAllowedDate();

    /**
     * Get version Id
     *
     * @return mixed
     */
    public function getVersionId();

    /**
     * Get remote ip
     *
     * @return mixed
     */
    public function getRemoteIp();

    /**
     * Get file name
     *
     * @return mixed
     */
    public function getFile();

    /**
     * Set id
     *
     * @param int $Id
     * @return mixed
     */
    public function setId($Id);

    /**
     * Set created at
     *
     * @param mixed $createdAt
     * @return mixed
     */
    public function setCreatedAt($createdAt);

    /**
     * Set status
     *
     * @param int $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * Set product id
     *
     * @param int $productId
     * @return mixed
     */
    public function setProductId($productId);

    /**
     * Set package name
     *
     * @param string $name
     * @return mixed
     */
    public function setName($name);

    /**
     * Set Package Description
     *
     * @param string $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * Set package repository url
     *
     * @param string $repositoryUrl
     * @return mixed
     */
    public function setRepositoryUrl($repositoryUrl);

    /**
     * Set package repository options
     *
     * @param mixed $repositoryOption
     * @return mixed
     */
    public function setRepositoryOption($repositoryOption);

    /**
     * Set package generated packages.json
     *
     * @param mixed $packageJson
     * @return mixed
     */
    public function setPackageJson($packageJson);

    /**
     * Set version
     *
     * @param string $version
     * @return mixed
     */
    public function setVersion($version);

    /**
     * Set bundled package
     *
     * @param  mixed $bundledPackage
     * @return mixed
     */
    public function setBundledPackage($bundledPackage);

    /**
     * Set package default
     *
     * @param mixed $default
     * @return mixed
     */
    public function setDefault($default);

    /**
     * Set Customer id
     *
     * @param int $customerId
     * @return mixed
     */
    public function setCustomerId($customerId);

    /**
     * Set order id
     *
     * @param int $orderId
     * @return mixed
     */
    public function setOrderId($orderId);

    /**
     * Set package Id
     *
     * @param int $packageId
     * @return mixed
     */
    public function setPackageId($packageId);

    /**
     * Set last allow version
     *
     * @param string $lastAllowedVersion
     * @return mixed
     */
    public function setLastAllowedVersion($lastAllowedVersion);

    /**
     * Set Last allowed date
     *
     * @param mixed $lastAllowedDate
     * @return mixed
     */
    public function setLastAllowedDate($lastAllowedDate);

    /**
     * Set Function
     *
     * @param string $versionId
     * @return mixed
     */
    public function setVersionId($versionId);

    /**
     * Set remote Ip
     *
     * @param string $remoteIp
     * @return mixed
     */
    public function setRemoteIp($remoteIp);

    /**
     * Set File
     *
     * @param string $file
     * @return mixed
     */
    public function setFile($file);
}
