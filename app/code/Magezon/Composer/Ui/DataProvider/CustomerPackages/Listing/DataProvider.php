<?php

namespace Magezon\Composer\Ui\DataProvider\CustomerPackages\Listing;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Magezon\Composer\Api\Data\CustomerPackagesInterface;
use Magezon\Composer\Model\CustomerPackagesFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Psr\Log\LoggerInterface;

class DataProvider extends AbstractDataProvider
{
    private const ORDER_COMPLETE = "complete";
    private const ORDER_PAID = "2";
    /**
     * @var AbstractCollection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CustomerPackagesRepositoryInterface
     */
    private $customerPackagesRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    private $logger;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CustomerPackagesRepositoryInterface $customerPackagesRepository
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CustomerPackagesRepositoryInterface $customerPackagesRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $customerPackagesRepository->getCustomerPackagesCollection();
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Get Customer Packages grid data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $salesCollection = $this->collection;
        $salesCollection->addFieldToFilter("order.status", self::ORDER_COMPLETE)
            ->addFieldToFilter("invoice.state", self::ORDER_PAID);
        $items = $salesCollection->getData();
        foreach ($items as $item) {
            $selectItem = [];
            $selectItem['order_id'] = str_pad($item['order_id'], 10, '0', STR_PAD_LEFT);
            $selectItem['package_id'] = $item['package_id'];
            $selectItem['customer_id'] = $item['customer_id'];
            $selectItem['name'] = $item['name'];
            $selectItem['entity_id'] = uniqid();
            $this->loadedData[] = $selectItem;
        };
        $pagesize    = intval($this->request->getParam('paging')['pageSize']);
        $pageCurrent = intval($this->request->getParam('paging')['current']);
        $pageoffset  = ($pageCurrent - 1) * $pagesize;
        return [
            'totalRecords' => is_array($this->loadedData)? count($this->loadedData): 0,
            'items'        => is_array($this->loadedData)?array_slice($this->loadedData, $pageoffset, $pagesize): []
        ];
    }

 
    /**
      * Adding Order To Grid Collection
      *
      */
 
    public function addOrder($field, $direction)
     {
         $this->searchCriteriaBuilder->addSortOrder($field, $direction);
     }
 
    /**
      * Set Limit To Admin Collection
      *
      */
 
    public function setLimit($offset, $size)
     {
         $this->searchCriteriaBuilder->setPageSize($size);
         $this->searchCriteriaBuilder->setCurrentPage($offset);
    }
}
