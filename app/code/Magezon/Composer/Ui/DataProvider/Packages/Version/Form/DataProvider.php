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

namespace Magezon\Composer\Ui\DataProvider\Packages\Version\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magezon\Composer\Model\ResourceModel\Packages\Version\CollectionFactory;
use Psr\Log\LoggerInterface;

class DataProvider extends AbstractDataProvider
{
    private const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * @var PoolInterface
     */
    private $pool;
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * DataProvider constructor.
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param UrlInterface $urlBuilder
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param LoggerInterface $logger
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        UrlInterface $urlBuilder,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_logger = $logger;
        $this->urlBuilder = $urlBuilder;
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

        foreach ($items as $version) {
            $data = $version->getData();
            if (isset($data['zip'])) {
                $zipData = json_decode($data['zip'], true);
                unset($data['zip']);
                $data['zip'][0] = [
                    'name' => $zipData['name'],
                    'size' => $zipData['size'],
                    'type' => $zipData['type'],
                    'url' => $this->urlBuilder->getUrl('*/*/download',["version_id" => $version->getData('id'),"name" => $zipData['name']])
                ];
            }


            $this->data[$version->getId()] = $data;
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
//        $this->pool->getModifiers();
//
//        foreach ($this->pool->getModifiersInstances() as $modifier) {
//            $this->meta = $modifier->modifyMeta($this->meta);
//        }

        return $this->meta;
    }
}
