<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Composer
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

namespace Magezon\Composer\Block\Packages\Versions;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magezon\Composer\Model\ResourceModel\Packages\Version\Collection;
use Magezon\Composer\Model\ResourceModel\Packages\Version\CollectionFactory;
use Psr\Log\LoggerInterface;

class Edit extends Template
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var CollectionFactory
     */
    private $versionCollectionFactory;
    /**
     * @var Collection
     */
    private $versionCollection;
    /**
     * @var SessionFactory
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param CollectionFactory $versionCollectionFactory
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SessionFactory $customerSession,
        CollectionFactory $versionCollectionFactory,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->_logger = $logger;
        $this->versionCollectionFactory = $versionCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get current customer
     *
     * Return stored customer or get it from session
     *
     * @since 102.0.1
     */
    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if ($customer === null) {
            $customer = $this->customerSession->create()->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * Get current verson info
     *
     * @param string $version_id
     * @return DataObject
     * @since 102.0.1
     */
    public function getVersion(string $version_id)
    {
        if (null === $this->versionCollection) {
            $collection = $this->versionCollectionFactory->create();
            $collection->addFieldToFilter('main_table.id', ['in' => $version_id]);
            $this->versionCollection = $collection;
        }
        return $this->versionCollection->getFirstItem();
    }

    /**
     * Get link to download zip version
     *
     * @param string $version_id
     * @return string
     */
    public function getVersionDownloadUrl(string $version_id):string
    {
        return $this->getUrl('composer/packages/download/', ['version_id' => $version_id]);
    }

    /**
     * Generate and return "New Access Key" URL
     *
     * @return string
     * @since 102.0.1
     */
    public function getSaveUrl(): string
    {
        return $this->getUrl('composer/packages/save', ['_secure' => true]);
    }
}
