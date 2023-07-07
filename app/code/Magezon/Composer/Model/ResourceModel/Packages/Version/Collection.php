<?php

namespace Magezon\Composer\Model\ResourceModel\Packages\Version;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magezon\Composer\Model\Packages\Version;
use Magezon\Composer\Model\ResourceModel\Packages\Version as VersionResource;

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
            Version::class,
            VersionResource::class
        );
    }
    /**
     * Select and join package table
     *
     * @return Collection|void
     */
    protected function _initSelect()
    {
        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('version', 'main_table.version');
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['packages' => $this->getTable('mgz_package_packages')],
            'main_table.package_id = packages.id',
            ['packages.name']
        );
    }
}
