<?php

namespace Magezon\Composer\Model\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magezon\Composer\Api\Data\PackagesInterface;
use Magezon\Composer\Api\Data\PackagesSearchResultsInterfaceFactory;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Model\PackagesFactory;
use Magezon\Composer\Model\ResourceModel\Packages as PackagesResources;
use Magezon\Composer\Model\ResourceModel\Packages\Collection as CollectionPackages;
use Magezon\Composer\Model\ResourceModel\Packages\CollectionFactory as CollectionPackagesFactory;
use Psr\Log\LoggerInterface;

/**
 * Class PackagesRepository Impl
 */
class PackagesRepository implements PackagesRepositoryInterface
{
    /**
     * @var array
     */
    private $instances = [];
    /**
     * @var PackagesResources
     */
    private $resource;
    /**
     * @var PackagesInterface
     */
    private $composer;
    /**
     * @var PackagesFactory
     */
    private $packagesFactory;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var CollectionPackagesFactory
     */
    private $collectionPackagesFactory;
    /**
     * @var PackagesSearchResultsInterfaceFactory
     */
    private $packagesSearchResultsInterfaceFactory;
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * CustomerAuthRepository constructor.
     * @param PackagesResources $resource
     * @param PackagesInterface $composer
     * @param PackagesFactory $packagesFactory
     * @param ManagerInterface $messageManager
     * @param CollectionPackagesFactory $collectionPackagesFactory
     * @param PackagesSearchResultsInterfaceFactory $packagesSearchResultsInterfaceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        PackagesResources $resource,
        PackagesInterface $composer,
        PackagesFactory $packagesFactory,
        ManagerInterface $messageManager,
        CollectionPackagesFactory $collectionPackagesFactory,
        PackagesSearchResultsInterfaceFactory $packagesSearchResultsInterfaceFactory,
        LoggerInterface $logger
    ) {
        $this->resource                              = $resource;
        $this->composer                              = $composer;
        $this->collectionPackagesFactory             = $collectionPackagesFactory;
        $this->packagesFactory                       = $packagesFactory;
        $this->messageManager                        = $messageManager;
        $this->packagesSearchResultsInterfaceFactory = $packagesSearchResultsInterfaceFactory;
        $this->_logger = $logger;
    }

    /**
     * Save function
     *
     * @param PackagesInterface $composer
     * @return PackagesInterface
     * @throws \Exception
     */
    public function save(PackagesInterface $composer)
    {
        try {
            if (!$composer->getId()) {
                $composer->setId(null);
            }
            $this->resource->save($composer);
        } catch (Exception $e) {
            $this->_logger->debug($e->getMessage());
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the package ' . $e->getMessage()
                );
        }

        return $composer;
    }
    /**
     * Get by Id
     *
     * @param int $composerId
     * @return PackagesInterface
     */
    public function getById($composerId)
    {
        if (!isset($this->instances[$composerId])) {
            $composer = $this->packagesFactory->create();
            $this->resource->load($composer, $composerId);
            $this->instances[$composerId] = $composer;
        }
        return $this->instances[$composerId];
    }
    /**
     * Delate package
     *
     * @param PackagesInterface $composer
     * @return bool
     * @throws \Exception
     */
    public function delete(PackagesInterface $composer)
    {
        $id = $composer->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($composer);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the package');
        }
        unset($this->instances[$id]);
        return true;
    }
    /**
     * Delete by id
     *
     * @param int $composerId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($composerId)
    {
        $composer = $this->getById($composerId);
        return $this->delete($composer);
    }

    /**
     * Get list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionPackagesFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * Add filter
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionPackages $collection
     * @return void
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, CollectionPackages $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Add sort order
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionPackages $collection
     * @return void
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, CollectionPackages $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * Add paging
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionPackages $collection
     * @return void
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, CollectionPackages $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * Build search result
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionPackages $collection
     * @return mixed
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, CollectionPackages $collection)
    {
        $searchResults = $this->packagesSearchResultsInterfaceFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Get by package name
     *
     * @param string $packageName
     * @return PackagesInterface
     */
    public function getByPackageName($packageName)
    {
        $composer = $this->packagesFactory->create();
        $this->resource->load($composer, $packageName, 'name');
        $id = $composer->getId();
        if (!$id) {
            return null;
        }
        return $composer;
    }

    /**
     * Get by package name
     *
     * @param string $sku
     * @return PackagesInterface
     */
    public function getBySku($sku)
    {
        $composer = $this->packagesFactory->create();
        $this->resource->load($composer, $sku, 'sku');
        $id = $composer->getId();
        if (!$id) {
            return null;
        }
        return $composer;
    }

    /**
     * @param string $name
     * @param string $version
     * @param string $package_json
     * @return mixed|void
     * @throws \Exception
     */
    public function updatePackageVersion($name, $version, $package_json)
    {
        $package = $this->getBySku($name);
        if (!$package) $package = $this->getByPackageName($name);
        if ($package) {
            $package->setPackageJson($package_json);
            $package->setVersion($version);
            $this->save($package);
            return $version;
        }
    }
}
