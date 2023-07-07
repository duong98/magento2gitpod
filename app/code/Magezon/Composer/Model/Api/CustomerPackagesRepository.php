<?php

namespace Magezon\Composer\Model\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory as SalesOrderGridCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as SalesOrderItemCollectionFactory;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Magezon\Composer\Api\Data\CustomerPackagesInterface;
use Magezon\Composer\Api\Data\CustomerPackagesSearchResultsInterfaceFactory;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Model\CustomerPackagesFactory;
use Magezon\Composer\Model\ResourceModel\CustomerPackages as PackagesResources;
use Magezon\Composer\Model\ResourceModel\Packages\Collection as PackagesCollection;
use Magezon\Composer\Model\ResourceModel\Packages\CollectionFactory as PackagesCollectionFactory;
use Magezon\Composer\Model\ResourceModel\CustomerPackages\Collection as CollectionPackages;
use Magezon\Composer\Model\ResourceModel\CustomerPackages\CollectionFactory as CollectionPackagesFactory;

/**
 * Class PackagesRepository Impl
 */
class CustomerPackagesRepository implements CustomerPackagesRepositoryInterface
{
    private const ORDER_COMPLETED = "complete";
    private const ORDER_PAID = "2";
    private const ORDER_CLOSED = "closed";
    private const IS_PACKAGE_SHARED = 1;
    /**
     * @var array
     */
    private $instances = [];
    /**
     * @var PackagesResources
     */
    private $resource;
    /**
     * @var CustomerPackagesInterface
     */
    private $composer;

    /**
     * @var AccessKeysRepositoryInterface
     */
    private $accessKeysRepository;

    /**
     * @var PackagesRepositoryInterface
     */
    private $packagesRepository;

    /**
     * @var CustomerPackagesFactory
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
     * @var PackagesCollectionFactory
     */
    private $packagesCollectionFactory;
    /**
     * @var CustomerPackagesSearchResultsInterfaceFactory
     */
    private $packagesSearchResultsInterfaceFactory;

    /**
     * @var SalesOrderGridCollectionFactory
     */
    private $salesOrderCollectionFactory;
    /**
     * @var SalesOrderItemCollectionFactory
     */
    private $salesOrderItemCollectionFactory;

    /**
     * CustomerAuthRepository constructor.
     * @param PackagesResources $resource
     * @param CustomerPackagesInterface $composer
     * @param CustomerPackagesFactory $packagesFactory
     * @param ManagerInterface $messageManager
     * @param CollectionPackagesFactory $collectionPackagesFactory
     * @param PackagesCollectionFactory $packagesCollectionFactory
     * @param CustomerPackagesSearchResultsInterfaceFactory $packagesSearchResultsInterfaceFactory
     * @param SalesOrderGridCollectionFactory $salesOrderCollectionFactory
     * @param AccessKeysRepositoryInterface $accessKeysRepository
     * @param PackagesRepositoryInterface $packagesRepository
     * @param SalesOrderItemCollectionFactory $salesOrderItemCollectionFactory
     */
    public function __construct(
        PackagesResources $resource,
        CustomerPackagesInterface $composer,
        CustomerPackagesFactory $packagesFactory,
        ManagerInterface $messageManager,
        CollectionPackagesFactory $collectionPackagesFactory,
        PackagesCollectionFactory $packagesCollectionFactory,
        CustomerPackagesSearchResultsInterfaceFactory $packagesSearchResultsInterfaceFactory,
        SalesOrderGridCollectionFactory $salesOrderCollectionFactory,
        AccessKeysRepositoryInterface $accessKeysRepository,
        PackagesRepositoryInterface $packagesRepository,
        SalesOrderItemCollectionFactory $salesOrderItemCollectionFactory
    ) {
        $this->resource                              = $resource;
        $this->composer                              = $composer;
        $this->collectionPackagesFactory             = $collectionPackagesFactory;
        $this->packagesFactory                       = $packagesFactory;
        $this->messageManager                        = $messageManager;
        $this->packagesSearchResultsInterfaceFactory = $packagesSearchResultsInterfaceFactory;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->accessKeysRepository = $accessKeysRepository;
        $this->packagesRepository = $packagesRepository;
        $this->salesOrderItemCollectionFactory = $salesOrderItemCollectionFactory;
        $this->packagesCollectionFactory = $packagesCollectionFactory;
    }

    /**
     * Save function
     *
     * @param CustomerPackagesInterface $composer
     * @return CustomerPackagesInterface
     * @throws \Exception
     */
    public function save(CustomerPackagesInterface $composer)
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
     * Join with Sales Collection by package name
     *
     * @return mixed
     */
    public function getSalesCollection()
    {
        $salesOrderItemCollection = $this->salesOrderItemCollectionFactory->create();
        $salesOrderItemCollection->join(
            ["order" => "sales_order_grid"],
            "main_table.order_id = order.entity_id",
            ["order.customer_id", "order.status"]
        )->join(
            ["invoice" => "sales_invoice_grid"],
            "order.entity_id = invoice.order_id",
            ["invoice.state"]
        );
        return $salesOrderItemCollection;
    }

    /**
     * Get which package belong to which customer collection
     *
     * @return mixed
     */
    public function getCustomerPackagesCollection()
    {
        $salesOrderItemCollection = $this->salesOrderItemCollectionFactory->create();
        $salesOrderItemCollection->join(
            ["order" => "sales_order_grid"],
            "main_table.order_id = order.entity_id",
            ["order.customer_id"]
        )->join(
            ['packages' => 'mgz_package_packages'],
            'main_table.sku = packages.sku',
            ["packages.name","package_id" => "packages.id"]
        )->join(
            ["invoice" => "sales_invoice_grid"],
            "order.entity_id = invoice.order_id",
            ["invoice.state"]
        );
        $salesOrderItemCollection->addFilterToMap('name', 'packages.name');
        $salesOrderItemCollection->addFilterToMap('order_id', 'main_table.order_id');
        return $salesOrderItemCollection;
    }
    /**
     * Get by Id
     *
     * @param int $composerId
     * @return array
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
     * @param CustomerPackagesInterface $composer
     * @return bool
     * @throws \Exception
     */
    public function delete(CustomerPackagesInterface $composer)
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
     * Check if api keys is validated.
     *
     * @param string $auth
     * @param string $secret
     * @param string $pName
     * @return bool
     */
    public function checkAuth(string $auth, string $secret, string $pName)
    {
        // check if customer already paid for package
        $packageName  = str_replace('_', '/', $pName);
        $customerId = $this->accessKeysRepository->getCustomerId($auth, $secret);
        if (!$customerId) {
            return false;
        }
        $package = $this->packagesRepository->getBySku(str_replace("magezon/module-","",$packageName));
        if ($package->getData("shared_package") == 1) return true;
        $productSku = $package->getData('sku');
        $salesOrderItemCollection = $this->salesOrderItemCollectionFactory->create();
        $salesOrderItemCollection->join(
            ["order" => "sales_order_grid"],
            "main_table.order_id = order.entity_id",
            ["order.customer_id", "order.status"]
        )->join(
            ["invoice" => "sales_invoice_grid"],
            "order.entity_id = invoice.order_id",
            ["invoice.state"]
        );
        $salesOrderItemCollection->addFieldToFilter("sku", $productSku)
            ->addFieldToFilter("customer_id", $customerId)->addFieldToFilter("order.status", self::ORDER_COMPLETED)
        ->addFieldToFilter("invoice.state", self::ORDER_PAID);
        if ($salesOrderItemCollection->getTotalCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get authenticated packages list by customer id.
     *
     * @param string $customer_id
     * @return mixed
     */
    public function getAuthList(string $customer_id)
    {
        $packagesCollection = $this->packagesCollectionFactory->create();
        $packagesCollection->join(
            ['items' => 'sales_order_item'],
            'main_table.sku = items.sku',
            ['main_table.id','main_table.name','main_table.status']
        )->join(
            ["order" => "sales_order_grid"],
            "items.order_id = order.entity_id",
            ["order.customer_id", "order_status" => "order.status"]
        )->join(
            ["invoice" => "sales_invoice_grid"],
            "order.entity_id = invoice.order_id",
            ["invoice.state"]
        );
        $packagesCollection
            ->addFieldToFilter("customer_id", $customer_id)->addFieldToFilter("order.status", self::ORDER_COMPLETED)
            ->addFieldToFilter("invoice.state", self::ORDER_PAID);
        $packagesCollection->getSelect()->orWhere("main_table.shared_package = 1");
        $packagesCollection->getSelect()->group("main_table.id");

        return $packagesCollection;
    }
    /**
     * Get authenticated packages list by customer id.
     *
     * @param string $auth
     * @param string $secret
     * @return mixed
     */
    public function getAuthListByKeys(string $auth, string $secret)
    {
        $customerId = $this->accessKeysRepository->getCustomerId($auth, $secret);
        if (!$customerId) {
            return false;
        }
        $packagesCollection = $this->packagesCollectionFactory->create();
        $packagesCollection->join(
            ['items' => 'sales_order_item'],
            'main_table.sku = items.sku',
            ['main_table.id','main_table.name','main_table.status']
        )->join(
            ["order" => "sales_order_grid"],
            "items.order_id = order.entity_id",
            ["order.customer_id", "order_status" => "order.status"]
        )->join(
            ["invoice" => "sales_invoice_grid"],
            "order.entity_id = invoice.order_id",
            ["invoice.state"]
        );
        $packagesCollection
            ->addFieldToFilter("customer_id", $customerId)->addFieldToFilter("order.status", self::ORDER_COMPLETED)
            ->addFieldToFilter("invoice.state", self::ORDER_PAID);
        $packagesCollection->getSelect()->group("main_table.id");
        $items = $packagesCollection->getItems();
        $result = [];
        foreach ($items as $item) {
            $newData = [];
            $newData[]['name'] = $item->getData('sku');
            $newData[]['package_json'] = $item->getData('package_json');
            $result[] = $newData;
        }
        $sharedPackagesCollection = $this->packagesCollectionFactory->create();
        $sharedPackagesCollection->getSelect()->where('shared_package = 1');
        $sharedItems = $sharedPackagesCollection->getItems();
        foreach ($sharedItems as $item) {
            $newData = [];
            $newData[]['name'] = $item->getData('name');
            $newData[]['package_json'] = $item->getData('package_json');
            $result[] = $newData;
        }

        return $result;
    }
}
