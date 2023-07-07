<?php

namespace Magezon\Composer\Model\ResourceModel\Packages;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Version Package
 */
class Version extends AbstractDb
{
    /**
     * Package Version constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("mgz_package_packages_versions", 'id');
    }

    /**
     * Create created date before save
     *
     * @param AbstractModel $objectModel
     * @return Version
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $objectModel): Version
    {
        if (!$objectModel->getId() && !$objectModel->getData("created_at")) {
            $objectModel->setData("created_at", date("Y-m-d H:i:s"));
        }

        return parent::_beforeSave($objectModel);
    }
}
