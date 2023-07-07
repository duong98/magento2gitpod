<?php

namespace Magezon\Composer\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Packages Setup
 */
class CustomerPackages extends AbstractDb
{
    /**
     * Package constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("mgz_package_customer_packages", "id");
    }

    /**
     * Create created date before save
     *
     * @param AbstractModel $objectModel
     * @return CustomerPackages
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $objectModel): CustomerPackages
    {
        if (!$objectModel->getId() && !$objectModel->getData("created_at")) {
            $objectModel->setData("created_at", date("Y-m-d H:i:s"));
        }
        return parent::_beforeSave($objectModel);
    }
}
