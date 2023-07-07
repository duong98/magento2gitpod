<?php
namespace Magezon\Composer\Model\ResourceModel\AccessKeys;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magezon\Composer\Model\AccessKeys;
use Magezon\Composer\Model\ResourceModel\AccessKeys as AccessKeysResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'access_id';

    /**
     * Access Keys Collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            AccessKeys::class,
            AccessKeysResourceModel::class
        );
    }
}
