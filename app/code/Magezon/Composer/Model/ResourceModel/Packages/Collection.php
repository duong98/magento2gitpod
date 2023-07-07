<?php

namespace Magezon\Composer\Model\ResourceModel\Packages;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magezon\Composer\Model\Packages;
use Magezon\Composer\Model\ResourceModel\Packages as PackagesResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            Packages::class,
            PackagesResource::class
        );

        $this->_map['composer']['id'] = 'main_table.id';
    }
}
