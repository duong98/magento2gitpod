<?php
namespace Magezon\Composer\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AccessKeys extends AbstractDb
{
    /**
     * Access Keys Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("mgz_package_accesskeys", "access_id");
    }
    /**
     * Create created date before save
     *
     * @param AbstractModel $objectModel
     * @return AccessKeys
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $objectModel): AccessKeys
    {
        if (!$objectModel->getId() && !$objectModel->getData("created_at")) {
            $objectModel->setData("created_at", date("Y-m-d H:i:s"));
        }
        return parent::_beforeSave($objectModel);
    }
}
