<?php

namespace Magezon\Composer\Model\Packages;

use Magento\Framework\Model\AbstractModel;
use Magezon\Composer\Api\Data\VersionInterface;
use Magezon\Composer\Model\ResourceModel\Packages\Version as VersionResource;

/**
 * Version Model Class
 */
class Version extends AbstractModel implements VersionInterface
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init(VersionResource::class);
    }
}
