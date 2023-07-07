<?php

namespace Magezon\Composer\Observer\Sales;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Magezon\Composer\Api\Data\AccessKeysInterface;
use Magezon\Composer\Api\Data\CustomerPackagesInterface;
use Magezon\Composer\Api\Data\PackagesInterface;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Helper\KeyGenerator;
use Magezon\Composer\Model\AccessKeysFactory;
use Magezon\Composer\Model\CustomerPackagesFactory;
use Psr\Log\LoggerInterface;

/**
 * Observer add authorize customer to package after purchasing
 */
class SalesOrderInvoicePay implements ObserverInterface
{
    /**
     * @var PackagesInterface
     */
    private $packages;
    /**
     * @var CustomerPackagesRepositoryInterface
     */
    private $customerPackagesRepository;
    /**
     * @var CustomerPackagesFactory
     */
    private $customerPackagesFactory;
    /**
     * @var AccessKeysInterface
     */
    private $customerAuth;
    /**
     * @var AccessKeysFactory
     */
    private $customerAuthFactory;
    /**
     * @var AccessKeysRepositoryInterface
     */
    private $customerAuthRepository;
    /**
     * @var CustomerPackagesInterface
     */
    private $customerPackages;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var KeyGenerator
     */
    private $dataHelper;
    /**
     * @var PackagesRepositoryInterface
     */
    private $composerRepoRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * SalesOrderInvoicePay constructor.
     * @param PackagesInterface $packages
     * @param KeyGenerator $dataHelper
     * @param AccessKeysInterface $customerAuth
     * @param AccessKeysFactory $customerAuthFactory
     * @param AccessKeysRepositoryInterface $customerAuthRepository
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilder $searchCriteria
     * @param CustomerPackagesInterface $customerPackages
     * @param CustomerPackagesRepositoryInterface $customerPackagesRepository
     * @param CustomerPackagesFactory $customerPackagesFactory
     * @param PackagesRepositoryInterface $composerRepoRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        PackagesInterface $packages,
        KeyGenerator $dataHelper,
        AccessKeysInterface $customerAuth,
        AccessKeysFactory $customerAuthFactory,
        AccessKeysRepositoryInterface $customerAuthRepository,
        LoggerInterface $logger,
        SearchCriteriaBuilder $searchCriteria,
        CustomerPackagesInterface $customerPackages,
        CustomerPackagesRepositoryInterface $customerPackagesRepository,
        CustomerPackagesFactory $customerPackagesFactory,
        PackagesRepositoryInterface $composerRepoRepository
    ) {
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->packages = $packages;
        $this->customerAuth = $customerAuth;
        $this->customerAuthRepository = $customerAuthRepository;
        $this->customerAuthFactory = $customerAuthFactory;
        $this->customerPackages = $customerPackages;
        $this->searchCriteria = $searchCriteria;
        $this->customerPackagesRepository = $customerPackagesRepository;
        $this->customerPackagesFactory = $customerPackagesFactory;
        $this->composerRepoRepository = $composerRepoRepository;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(
        Observer $observer
    ) {
        $event = $observer->getEvent();
        /** @var Invoice $invoice */
        $invoice = $event->getInvoice();
        /** @var Order $order */
        $order = $invoice->getOrder();
        $customerId = $order->getCustomerId();

        $searchCriteriaBuilder = $this->searchCriteria;
        $itemsCollection = $invoice->getItemsCollection();
        /** @var Item $item */
        foreach ($itemsCollection as $item) {
            $searchCriteria = $searchCriteriaBuilder->addFilter(
                'product_id',
                $item->getProductId(),
                'eq'
            )->create();
            $package = $this->composerRepoRepository->getList($searchCriteria);
            $items = $package->getItems();
            $lastElementPackage = end($items);

            if ($lastElementPackage instanceof PackagesInterface) {
                $customerPackage = $this->customerPackagesFactory->create();
                $customerPackage->setStatus(1);
                $customerPackage->setCustomerId($customerId);
                $customerPackage->setOrderId($order->getIncrementId());
                $customerPackage->setPackageId($lastElementPackage->getId());
                $customerPackage->setLastAllowedVersion($lastElementPackage->getVersion());

                $period = $this->dataHelper->period();
                if ($period) {
                    $endDate = new \DateTime();
                    $endDate->add(new \DateInterval('P' . int($period) . 'M'));

                    $customerPackage->setLastAllowedDate($endDate->format('Y-m-d H:i:s'));
                }
                try {
                    $this->customerPackagesRepository->save($customerPackage);
                } catch (\Exception $e) {
                    $this->logger->info($e->getMessage());
                }
            }
        }

        $authKey = $this->dataHelper->generateKey();
        $secretAuthKey = $this->dataHelper->generateSecret();

        $customerKey = $this->customerAuthFactory->create();
        $customerKey->setStatus(1);
        $customerKey->setCustomerId($customerId);
        $customerKey->setAccessKey($authKey);
        $customerKey->setAccessSecret($secretAuthKey);

        $this->customerAuthRepository->save($customerKey);
    }
}
