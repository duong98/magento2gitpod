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

namespace Magezon\Composer\Block\AccessKeys;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magezon\Composer\Model\ResourceModel\AccessKeys\Collection;
use Magezon\Composer\Model\ResourceModel\AccessKeys\CollectionFactory;
use Magezon\Composer\Helper\Data;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class Grid extends Template
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var SessionFactory
     */
    private $customerSession;

    /**
     * @var CollectionFactory
     */
    private $accessKeyCollectionFactory;

    /**
     * @var Collection
     */
    private $accessKeyCollection;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param CollectionFactory $accessKeyCollectionFactory
     * @param LoggerInterface $logger
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SessionFactory $customerSession,
        CollectionFactory $accessKeyCollectionFactory,
        LoggerInterface $logger,
        Data $helperData,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->accessKeyCollectionFactory = $accessKeyCollectionFactory;
        $this->_logger = $logger;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Prepare the accessKeyssection layout
     *
     * @return void
     * @throws LocalizedException
     * @since 102.0.1
     */
    protected function _prepareLayout(): void
    {
        parent::_prepareLayout();
        $this->preparePager();
    }

    /**
     * Generate and return "New Access Key" URL
     *
     * @return string
     * @since 102.0.1
     */
    public function getAddAccessKeyUrl(): string
    {
        return $this->getUrl('composer/accesskeys/new', ['_secure' => true]);
    }
    /**
     * Get max allowed keys config
     *
     * @return string
     * @since 102.0.1
     */
    public function getMaxAllowedKeysConfig(): string
    {
        return $this->helperData->getConfig('general/max_keys');
    }

    /**
     * Generate and return "Delete" URL
     *
     * @return string
     * @since 102.0.1
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('composer/accesskeys/delete');
    }

    /**
     * Generate and return "Enable AccessKey" URL.
     *
     * Access Keys ID passed in parameters
     *
     * @return string
     * @since 102.0.1
     */
    public function getAccessKeyEnableUrl(): string
    {
        return $this->getUrl('composer/accesskeys/enable');
    }

    /**
     * Generate and return "Disable AccessKey" URL.
     *
     * Access Keys ID passed in parameters
     *
     * @return string
     * @since 102.0.1
     */
    public function getAccessKeyDisableUrl(): string
    {
        return $this->getUrl('composer/accesskeys/disable');
    }

    /**
     * Get current additional customer accessKeyses
     *
     * Return array of accessKeys interfaces if customer has additional accessKeyses and false in other cases
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @since 102.0.1
     */
    public function getAdditionalAccessKeys(): Collection
    {
        return $this->getAccessKeysCollection();
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
     * Get pager layout
     *
     * @return void
     * @throws LocalizedException
     */
    private function preparePager(): void
    {
        $accessKeysCollection = $this->getaccessKeysCollection();
        if (null !== $accessKeysCollection) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'customer.accesskeys.pager'
            )->setCollection($accessKeysCollection);
            $this->setChild('pager', $pager);
        }
    }

    /**
     * Get customer accessKeys collection.
     *
     * Filters collection by customer id
     *
     * @return Collection
     * @throws NoSuchEntityException
     */
    private function getAccessKeysCollection(): Collection
    {
        if (null === $this->accessKeyCollection) {
            if (null === $this->getCustomer()) {
                throw new NoSuchEntityException(__('Customer not logged in'));
            }
            $collection = $this->accessKeyCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', ['in' => $this->getCustomer()->getId()]);
            $this->accessKeyCollection = $collection;
        }
        return $this->accessKeyCollection;
    }
}
