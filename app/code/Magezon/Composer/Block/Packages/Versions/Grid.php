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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Model\ResourceModel\Packages\Version\Collection;
use Magezon\Composer\Model\ResourceModel\Packages\Version\CollectionFactory;
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
    private $versionCollectionFactory;

    /**
     * @var Collection
     */
    private $versionCollection;
    /**
     * @var VersionRepositoryInterface
     */
    private $versionRepository;

    /**
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param CollectionFactory $versionCollectionFactory
     * @param VersionRepositoryInterface $versionRepository
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SessionFactory $customerSession,
        CollectionFactory $versionCollectionFactory,
        VersionRepositoryInterface $versionRepository,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->versionCollectionFactory = $versionCollectionFactory;
        $this->versionRepository = $versionRepository;
        $this->_logger = $logger;

        parent::__construct($context, $data);
    }

    /**
     * Prepare the Versionsection layout
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
    public function getAddPackageUrl(): string
    {
        return $this->getUrl('composer/packages/new', ['_secure' => true]);
    }

    /**
     * Generate and return "Delete" URL
     *
     * @return string
     * @since 102.0.1
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('composer/version/delete');
    }

    /**
     * Generate and return "Enable Package" URL.
     *
     * Access Keys ID passed in parameters
     *
     * @return string
     * @since 102.0.1
     */
    public function getPackageEnableUrl(): string
    {
        return $this->getUrl('composer/version/enable');
    }

    /**
     * Generate and return "Disable Package" URL.
     *
     * Access Keys ID passed in parameters
     *
     * @return string
     * @since 102.0.1
     */
    public function getPackageDisableUrl(): string
    {
        return $this->getUrl('composer/version/disable');
    }

    /**
     * Get current additional customer versiones
     *
     * @param string|null $package_id
     * Return array of version interfaces if customer has additional versiones and false in other cases
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @since 102.0.1
     */
    public function getAdditionalVersions(?string $package_id): Collection
    {
        return $this->getVersionsCollection($package_id);
    }

    /**
     * Get link to package versions
     *
     * @param string|null $package_id
     * @return string
     */
    public function getVersionsUrl(?string $package_id):string
    {
        if ($package_id) {
            return $this->getUrl('composer/packages/versions/', ['package_id' => $package_id]);
        } else {
            return $this->getUrl('composer/packages/versions/');
        }
    }

    /**
     * Get link to version Info
     *
     * @param string $version_id
     * @return string
     */
    public function getVersionInfoUrl(string $version_id):string
    {
        return $this->getUrl('composer/packages/version/', ['version_id' => $version_id]);
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
        $versionCollection = $this->getVersionCollection();
        if (null !== $versionCollection) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'customer.versions.pager'
            )->setCollection($versionCollection);
            $this->setChild('pager', $pager);
        }
    }

    /**
     * Get customer version collection.
     *
     * @param string|null $package_id
     * Filters collection by customer id
     *
     * @return Collection
     * @throws NoSuchEntityException
     */
    private function getVersionsCollection(?string $package_id): Collection
    {
        if (null === $this->versionCollection) {
            if (null === $this->getCustomer()) {
                throw new NoSuchEntityException(__('Customer not logged in'));
            }
            $collection = $this->versionCollectionFactory->create();
            if ($package_id) {
                $collection->addFieldToFilter('package_id', ['in' => $package_id]);
            }
            $collection->setOrder('version', 'ASC');
            $this->versionCollection = $collection;
        }
        return $this->versionCollection;
    }
}
