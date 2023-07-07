<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Composer
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

declare(strict_types=1);

namespace Magezon\Composer\Ui\DataProvider\Packages\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magezon\Composer\Model\ResourceModel\Packages\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private const META_CONFIG_PATH = '/arguments/data/config';
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * DataProvider constructor.
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get Packages data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getData()
    {
        $items = $this->collection->getItems();

        foreach ($items as $package) {
            $data = $package->getData();

            $this->data[$package->getId()] = $data;
        }
        $this->getConfigData();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * Get Access Keys metadata
     *
     * @return array
     * @throws LocalizedException
     */
    public function getMeta()
    {
        $this->meta = parent::getMeta();
        return $this->meta;
    }
}
