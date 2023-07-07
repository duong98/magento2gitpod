<?php

namespace Magezon\Composer\Model;

use Magento\Framework\Model\AbstractModel;
use Magezon\Composer\Api\Data\PackagesInterface;
use Magezon\Composer\Model\ResourceModel\Packages as ResourcePackages;

class Packages extends AbstractModel implements PackagesInterface
{
    public const CACHE_TAG = 'PACKAGES_JSON';
    public const CACHE_KEY = 'composer_repo_package_';

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    public const PACKAGE_NORMAL = 0;
    public const PACKAGE_NAME = 'name';
    public const PACKAGE_BUNDLE = 1;

    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init(ResourcePackages::class);
    }

//    public function getByPackageName($name)
//    {
//        return $this->load($name, 'name');
//    }
    public function getCreatedAt()
    {
        // TODO: Implement getCreatedAt() method.
    }

    public function getStatus()
    {
        // TODO: Implement getStatus() method.
    }

    public function getProductId()
    {
        // TODO: Implement getProductId() method.
    }

    /**
     * Get Package Name
     *
     * @return array|mixed|string|null
     */
    public function getName()
    {
        // TODO: Implement getName() method.
        return $this->getData(self::PACKAGE_NAME);
    }

    public function getDescription()
    {
        // TODO: Implement getDescription() method.
    }

    public function getRepositoryUrl()
    {
        // TODO: Implement getRepositoryUrl() method.
    }

    public function getRepositoryOption()
    {
        // TODO: Implement getRepositoryOption() method.
    }

    public function getPackageJson()
    {
        // TODO: Implement getPackageJson() method.
    }

    public function getVersion()
    {
        // TODO: Implement getVersion() method.
    }

    public function getBundledPackage()
    {
        // TODO: Implement getBundledPackage() method.
    }

    public function getDefault()
    {
        // TODO: Implement getDefault() method.
    }

    public function getCustomerId()
    {
        // TODO: Implement getCustomerId() method.
    }

    public function getOrderId()
    {
        // TODO: Implement getOrderId() method.
    }

    public function getPackageId()
    {
        // TODO: Implement getPackageId() method.
    }

    public function getLastAllowedVersion()
    {
        // TODO: Implement getLastAllowedVersion() method.
    }

    public function getLastAllowedDate()
    {
        // TODO: Implement getLastAllowedDate() method.
    }

    public function getVersionId()
    {
        // TODO: Implement getVersionId() method.
    }

    public function getRemoteIp()
    {
        // TODO: Implement getRemoteIp() method.
    }

    public function getFile()
    {
        // TODO: Implement getFile() method.
    }

    public function setCreatedAt($createdAt)
    {
        // TODO: Implement setCreatedAt() method.
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setProductId($productId)
    {
        // TODO: Implement setProductId() method.
    }

    public function setName($name)
    {
        // TODO: Implement setName() method.
    }

    public function setDescription($description)
    {
        // TODO: Implement setDescription() method.
    }

    public function setRepositoryUrl($repositoryUrl)
    {
        // TODO: Implement setRepositoryUrl() method.
    }

    public function setRepositoryOption($repositoryOption)
    {
        // TODO: Implement setRepositoryOption() method.
    }

    public function setPackageJson($packageJson)
    {
        $this->setData("package_json", $packageJson);
    }

    public function setVersion($version)
    {
        $this->setData("version", $version);
    }

    public function setBundledPackage($bundledPackage)
    {
        // TODO: Implement setBundledPackage() method.
    }

    public function setDefault($default)
    {
        // TODO: Implement setDefault() method.
    }

    public function setCustomerId($customerId)
    {
        // TODO: Implement setCustomerId() method.
    }

    public function setOrderId($orderId)
    {
        // TODO: Implement setOrderId() method.
    }

    public function setPackageId($packageId)
    {
        // TODO: Implement setPackageId() method.
    }

    public function setLastAllowedVersion($lastAllowedVersion)
    {
        // TODO: Implement setLastAllowedVersion() method.
    }

    public function setLastAllowedDate($lastAllowedDate)
    {
        // TODO: Implement setLastAllowedDate() method.
    }

    public function setVersionId($versionId)
    {
        // TODO: Implement setVersionId() method.
    }

    public function setRemoteIp($remoteIp)
    {
        // TODO: Implement setRemoteIp() method.
    }

    public function setFile($file)
    {
        // TODO: Implement setFile() method.
    }
}
