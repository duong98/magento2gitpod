<?php

namespace Magezon\Composer\Model\ResourceModel\CustomerPackages;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magezon\Composer\Model\CustomerPackages;
use Magezon\Composer\Model\ResourceModel\CustomerPackages as PackagesResource;

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
    // @codingStandardsIgnoreLine
    public function _construct()
    {
        $this->_init(
            CustomerPackages::class,
            PackagesResource::class
        );

        $this->_map['composer']['id'] = 'main_table.id';
    }

    /**
     * Select and join package table
     *
     * @return Collection|void
     */
    protected function _initSelect()
    {
        $this->addFilterToMap('status', 'main_table.status');
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['packages' => $this->getTable('mgz_package_packages')],
            'main_table.package_id = packages.id'
        );
    }
}
