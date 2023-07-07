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

namespace Magezon\Composer\Block\Packages;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Model\ResourceModel\CustomerPackages\Collection;
use Magezon\Composer\Model\ResourceModel\CustomerPackages\CollectionFactory;
use Magezon\Composer\Model\ResourceModel\Packages\Version\Collection as VersionCollection;
use Magezon\Composer\Model\ResourceModel\Packages\Version\CollectionFactory as VersionCollectionFactory;
use Magezon\Composer\Helper\Data;
use Psr\Log\LoggerInterface;

class Grid extends Template
{
    /**
     * @var VersionRepositoryInterface
     */
    private $versionRepository;
    /**
     * @var CustomerPackagesRepositoryInterface
     */
    private $customerPackagesRepository;
    /**
     * @var CollectionFactory
     */
    private $versionCollectionFactory;

    /**
     * @var VersionCollection
     */
    private $versionCollection;
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SessionFactory
     */
    private $customerSession;

    /**
     * @var CollectionFactory
     */
    private $packageCollectionFactory;

    /**
     * @var Collection
     */
    private $packageCollection;
    /**
     * @var PackagesRepositoryInterface
     */
    private $packageRepository;

    /**
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param CollectionFactory $packageCollectionFactory
     * @param PackagesRepositoryInterface $packageRepository
     * @param VersionCollectionFactory $versionCollectionFactory
     * @param VersionRepositoryInterface $versionRepository
     * @param CustomerPackagesRepositoryInterface $customerPackagesRepository
     * @param LoggerInterface $logger
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SessionFactory                                   $customerSession,
        CollectionFactory                                $packageCollectionFactory,
        PackagesRepositoryInterface                      $packageRepository,
        VersionCollectionFactory                         $versionCollectionFactory,
        VersionRepositoryInterface                       $versionRepository,
        CustomerPackagesRepositoryInterface              $customerPackagesRepository,
        LoggerInterface                                  $logger,
        Data $helperData,
        array                                            $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->packageRepository = $packageRepository;
        $this->versionCollectionFactory = $versionCollectionFactory;
        $this->versionRepository = $versionRepository;
        $this->_logger = $logger;
        $this->helperData = $helperData;
        $this->customerPackagesRepository = $customerPackagesRepository;

        parent::__construct($context, $data);
    }

    /**
     * Prepare the packagessection layout
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
        return $this->getUrl('composer/packages/delete');
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
        return $this->getUrl('composer/packages/enable');
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
        return $this->getUrl('composer/packages/disable');
    }

    /**
     * Get Composer Repository URL config.
     *
     * Access Keys ID passed in parameters
     *
     * @return string
     * @since 102.0.1
     */
    public function getComposerRepositoryUrl(): string
    {
        return $this->helperData->getConfig('general/composer_url');
    }

    /**
     * Get current additional customer packageses
     *
     * Return array of packages interfaces if customer has additional packageses and false in other cases
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @since 102.0.1
     */
    public function getAdditionalPackages()
    {
        return $this->getPackagesCollection();
    }

    /**
     * Get link to package versions
     *
     * @param string $package_id
     * @return string
     */
    public function getVersionsUrl(string $package_id):string
    {
        return $this->getUrl('composer/packages/versions/', ['package_id' => $package_id]);
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
        $packagesCollection = $this->getPackagesCollection();
        if (null !== $packagesCollection) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'customer.packages.pager'
            )->setCollection($packagesCollection);
            $this->setChild('pager', $pager);
        }
    }

    /**
     * Get customer packages collection.
     *
     * Filters collection by customer id
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getPackagesCollection()
    {
        if (null === $this->packageCollection) {
            if (null === $this->getCustomer()) {
                throw new NoSuchEntityException(__('Customer not logged in'));
            }
            $collection = $this->customerPackagesRepository->getAuthList($this->getCustomer()->getId());
            $this->packageCollection = $collection;
        }
        return $this->packageCollection;
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
    public function getAdditionalVersions(?string $package_id)
    {
        return $this->getVersionsCollection($package_id);
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
     * Get customer version collection.
     *
     * @param string|null $package_id
     * Filters collection by customer id
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getVersionsCollection(?string $package_id)
    {
        if (null === $this->getCustomer()) {
            throw new NoSuchEntityException(__('Customer not logged in'));
        }
        $collection = $this->versionCollectionFactory->create();
        if ($package_id) {
            $collection->addFieldToFilter('package_id', ['in' => $package_id]);
            if ($collection->getSize() < 1) {
                return null;
            }
        }
        $collection->setOrder('version_normalize', 'ASC');
        $this->versionCollection = $collection;
        return $this->versionCollection;
    }
}
