<?php
namespace Magezon\Composer\Model\Api;

use Cassandra\Exception\AlreadyExistsException;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Api\SortOrder;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\Data\AccessKeysInterface;
use Magezon\Composer\Api\Data\AccessKeysSearchResultsInterface;
use Magezon\Composer\Api\Data\AccessKeysSearchResultsInterfaceFactory;
use Magezon\Composer\Helper\KeyGenerator;
use Magezon\Composer\Model\Api\Data\AccessKeysFactory;
use Magezon\Composer\Model\ResourceModel\AccessKeys as AccessKeysResource;
use Magezon\Composer\Model\ResourceModel\AccessKeys\Collection as AccessKeysCollection;
use Magezon\Composer\Model\ResourceModel\AccessKeys\CollectionFactory;
use Psr\Log\LoggerInterface;

class AccessKeysRepository implements AccessKeysRepositoryInterface
{
    /**
     * @var AccessKeysFactory
     */
    protected $accessKeysFactory;
    /**
     * @var AccessKeysResource
     */
    protected $accessKeysResourceModel;
    /**
     * @var CollectionFactory
     */
    protected $accessKeysCollectionFactory;
    /**
     * @var KeyGenerator
     */
    protected $keyGenerator;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    /**
     * @var AccessKeysSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param AccessKeysFactory $accessKeysFactory
     * @param AccessKeysResource $accessKeysResourceModel
     * @param CollectionFactory $accessKeysCollectionFactory
     * @param AccessKeysSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param KeyGenerator $keyGenerator
     * @param SearchCriteriaBuilder $searchCriteria
     * @param LoggerInterface $logger
     */
    public function __construct(
        AccessKeysFactory                     $accessKeysFactory,
        AccessKeysResource                    $accessKeysResourceModel,
        CollectionFactory                     $accessKeysCollectionFactory,
        AccessKeysSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        KeyGenerator $keyGenerator,
        SearchCriteriaBuilder $searchCriteria,
        LoggerInterface $logger
    ) {
        $this->accessKeysFactory = $accessKeysFactory;
        $this->accessKeysResourceModel = $accessKeysResourceModel;
        $this->accessKeysCollectionFactory = $accessKeysCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->keyGenerator = $keyGenerator;
        $this->searchCriteria = $searchCriteria;
        $this->_logger = $logger;
    }

    /**
     * Save Access Key
     *
     * @param AccessKeysInterface $accessKey
     * @return AccessKeysInterface
     * @throws AlreadyExistsException|\Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(AccessKeysInterface $accessKey): AccessKeysInterface
    {
        if (!$accessKey->getId()) {
            $key = $this->keyGenerator->generateKey();
            $secret = $this->keyGenerator->generateSecret();
            $accessKey->setId(null);
            $accessKey->setAccessKey($key);
            $accessKey->setAccessSecret($secret);
        }
        $this->accessKeysResourceModel->save($accessKey);
        return $accessKey;
    }

    /**
     * Get Access key by Id
     *
     * @param int $accessId
     * @return AccessKeysInterface
     */
    public function getById(int $accessId): AccessKeysInterface
    {
        $accessKey = $this->accessKeysFactory->create();
        $this->accessKeysResourceModel->load($accessKey, $accessId);
        return $accessKey;
    }

    /**
     * Delete Many Access Keys
     *
     * @param AccessKeysInterface $accessKeys
     * @return bool
     * @throws Exception
     */
    public function delete(AccessKeysInterface $accessKeys): bool
    {
        $this->accessKeysResourceModel->delete($accessKeys);
        return true;
    }

    /**
     * Delete an access key by id
     *
     * @param int $accessId
     * @return bool
     * @throws Exception
     */
    public function deleteById(int $accessId): bool
    {
        return $this->delete($this->getById($accessId));
    }

    /**
     * Get All Access keys
     *
     * @param string $auth
     * @param string $secret
     * @return SearchResults
     */
    public function getAccessKeysList(string $auth, string $secret):SearchResults
    {
        $searchCriteriaBuilder = $this->searchCriteria;
        $authCriteria = $searchCriteriaBuilder
            ->addFilter('access_key', $auth, 'eq')
            ->addFilter('access_secret', $secret, 'eq')
            ->create();
        return $this->getList($authCriteria);
    }

    /**
     * Get All Access keys
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults
    {
        $collection = $this->accessKeysCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * Add filter to collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AccessKeysCollection $collection
     * @return void
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, AccessKeysCollection $collection)
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
     * Add sort order to access keys
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AccessKeysCollection $collection
     * @return void
     */
    private function addSortOrdersToCollection(
        SearchCriteriaInterface $searchCriteria,
        AccessKeysCollection $collection
    ) {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * Add pagination to access keys
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AccessKeysCollection $collection
     * @return void
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, AccessKeysCollection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * Build result access keys list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AccessKeysCollection $collection
     * @return AccessKeysSearchResultsInterface|SearchResults
     */
    private function buildSearchResult(
        SearchCriteriaInterface $searchCriteria,
        AccessKeysCollection $collection
    ) {
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $items = [];
        foreach ($collection->getItems() as $item) {
            $items[] = $item->getData();
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Get customer id by access keys pair
     *
     * @param string $auth
     * @param string $secret
     * @return string
     */
    public function getCustomerId(string $auth, string $secret): string
    {
        $collection = $this->accessKeysCollectionFactory->create();
        $collection->addFieldToFilter("access_key", $auth)->addFieldToFilter("access_secret", $secret);
        return $collection->getFirstItem()->getData("entity_id");
    }
}
