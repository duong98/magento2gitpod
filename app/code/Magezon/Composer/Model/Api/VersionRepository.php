<?php

namespace Magezon\Composer\Model\Api;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magezon\Composer\Api\Data\VersionInterface;
use Magezon\Composer\Api\Data\VersionSearchResultsInterface;
use Magezon\Composer\Api\Data\VersionSearchResultsInterfaceFactory;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Model\Packages\VersionFactory;
use Magezon\Composer\Api\Data\PackagesInterface;
use Magezon\Composer\Model\ResourceModel\Packages\Version as VersionResorceModel;
use Magezon\Composer\Model\ResourceModel\Packages\Version\Collection as CollectionVersion;
use Magezon\Composer\Model\ResourceModel\Packages\Version\CollectionFactory as CollectionVersionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class VersionRepository repo
 */
class VersionRepository implements VersionRepositoryInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var VersionInterface[]
     */
    private $instances = [];
    /**
     * @var PackagesInterface
     */
    private $packages;
    /**
     * @var VersionResorceModel
     */
    private $resource;
    /**
     * @var VersionInterface
     */
    private $composer;
    /**
     * @var VersionFactory
     */
    private $versionFactory;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var CollectionVersionFactory
     */
    private $collectionVersionFactory;
    /**
     * @var VersionSearchResultsInterfaceFactory
     */
    private $composerSearchResultsInterfaceFactory;
    /**
     * @var PackagesRepositoryInterface
     */
    private $packagesRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * CustomerVersionRepository constructor.
     * @param VersionResorceModel $resource
     * @param VersionInterface $composer
     * @param CollectionVersionFactory $collectionVersionFactory
     * @param VersionSearchResultsInterfaceFactory $composerSearchResultsInterfaceFactory
     * @param VersionFactory $versionFactory
     * @param ManagerInterface $messageManager
     * @param PackagesRepositoryInterface $packagesRepository
     * @param SearchCriteriaBuilder $searchCriteria
     */
    public function __construct(
        VersionResorceModel $resource,
        VersionInterface $composer,
        CollectionVersionFactory $collectionVersionFactory,
        VersionSearchResultsInterfaceFactory $composerSearchResultsInterfaceFactory,
        VersionFactory $versionFactory,
        ManagerInterface $messageManager,
        PackagesRepositoryInterface $packagesRepository,
        SearchCriteriaBuilder $searchCriteria,
        LoggerInterface $logger
    ) {
        $this->resource                              = $resource;
        $this->composer                              = $composer;
        $this->collectionVersionFactory              = $collectionVersionFactory;
        $this->versionFactory                        = $versionFactory;
        $this->messageManager                        = $messageManager;
        $this->composerSearchResultsInterfaceFactory = $composerSearchResultsInterfaceFactory;
        $this->packagesRepository                    = $packagesRepository;
        $this->searchCriteria = $searchCriteria;
        $this->_logger = $logger;
    }

    /**
     * Save version
     *
     * @param VersionInterface $composer
     * @return VersionInterface
     * @throws \Exception
     */
    public function save(VersionInterface $composer)
    {
        try {
            if (!$composer->getId()) {
                $composer->setId(null);
            }
            $this->resource->save($composer);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the package ' . $e->getMessage()
                );
        }

        return $composer;
    }

    /**
     * Get Version by id
     *
     * @param mixed $composerId
     * @return VersionInterface
     */
    public function getById($composerId)
    {
        if (!isset($this->instances[$composerId])) {
            $composer = $this->versionFactory->create();
            $this->resource->load($composer, $composerId);
            $this->instances[$composerId] = $composer;
        }
        return $this->instances[$composerId];
    }

    /**
     * Delete Version
     *
     * @param VersionInterface $composer
     * @return bool
     * @throws \Exception
     */
    public function delete(VersionInterface $composer)
    {
        $id = $composer->getEntityId();
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
     * Delete version by id
     *
     * @param mixed $composerId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($composerId)
    {
        $composer = $this->getById($composerId);
        return $this->delete($composer);
    }

    /**
     * Get version List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return VersionSearchResultsInterface|mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionVersionFactory->create();

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
     * @param CollectionVersion $collection
     * @return void
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, CollectionVersion $collection)
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
     * Add sorting
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionVersion $collection
     * @return void
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, CollectionVersion $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * Add pagination
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionVersion $collection
     * @return void
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, CollectionVersion $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * Build version list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param CollectionVersion $collection
     * @return VersionSearchResultsInterface
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, CollectionVersion $collection)
    {
        $searchResults = $this->composerSearchResultsInterfaceFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
    /**
     * Save versions by data
     *
     * @param string $name
     * @param string $versionOrigin
     * @param string $versionNr
     * @param string $distUrl
     * @param string $reference
     * @return mixed
     * @throws \Exception
     */
    public function saveVersions($name, $versionOrigin, $versionNr, $distUrl, $reference)
    {
        $this->packages = $this->packagesRepository->getBySku($name);
        if (!$this->packages) $this->packages = $this->packagesRepository->getByPackageName($name);
        if ($this->packages) {
            $package_id = $this->packages->getData('id');
            $searchCriteriaBuilder = $this->searchCriteria;
            $searchCriteria = $searchCriteriaBuilder
                ->addFilter('package_id', $package_id)
                ->addFilter('version', $versionOrigin)
                ->create();
            $versionModels = $this->getList($searchCriteria);
            $items = $versionModels->getItems();
            $versionModel = end($items);
            if (!$versionModel) {
                $versionFactory = $this->versionFactory->create();
                $versionFactory->setData("package_id", $package_id);
                $versionFactory->setData("file", $distUrl);
                $versionFactory->setData("status", 1);
                $versionFactory->setData("version_normalize", $versionNr);
                $versionFactory->setData("version", $versionOrigin);

                $versionModel = $this->save($versionFactory);
            }

//            if (!str_contains($versionModel->getData("file"), $reference)) {
//                $versionFactory = $this->versionFactory->create();
//                $id = $versionModel->getData("id");
//                $versionFactory->setData("id", $id);
//                $versionFactory->setData("file", $distUrl);
//                $versionFactory->setData("version_normalize", $versionNr);
//                $versionFactory->setData("status", 1);
//                $versionFactory->setData("version", $versionOrigin);
//
//                $this->save($versionFactory);
//            }
        }
    }
}
