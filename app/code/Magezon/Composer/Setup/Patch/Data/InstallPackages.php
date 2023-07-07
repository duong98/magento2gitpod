<?php

namespace Magezon\Composer\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Setup\PackageSetup;
use Psr\Log\LoggerInterface;
use Zend_Db_Select;

class InstallPackages implements \Magento\Framework\Setup\Patch\DataPatchInterface, \Magento\Framework\Setup\Patch\PatchVersionInterface
{
    protected $packageSetup;

    protected $productColFactory;

    protected $moduleDataSetup;

    protected $packagesRepository;
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PackageSetup $packageSetup
     * @param CollectionFactory $productColFactory
     * @param PackagesRepositoryInterface $packagesRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface                                       $moduleDataSetup,
        PackageSetup                                                   $packageSetup,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColFactory,
        PackagesRepositoryInterface $packagesRepository,
        LoggerInterface $logger
    ) {
        $this->packageSetup = $packageSetup;
        $this->productColFactory = $productColFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->packagesRepository = $packagesRepository;
        $this->_logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $items = $this->getProductData();
        foreach ($items as $item) {
            $sku = $item->getSku();
            $product_id = $item->getId();
            $data["name"] = $sku;
            $data["sku"] = $sku;
            $data["description"] = $sku;
            $data["product_id"] = $product_id;
            $package = $this->packageSetup->createPackage(['data' => $data]);
            $this->packagesRepository->save($package);
        }
        $this->moduleDataSetup->endSetup();
    }

    private function getProductData() {
        $productCollection = $this->productColFactory->create();
        $productCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(['entity_id','sku']);
        $productCollection->addFieldToFilter('visibility', ['neq' => 1])
            ->addFieldToFilter('status', ["eq" => 1])
            ->addFieldToFilter('type_id', ["eq" => "downloadable"]);
        $this->_logger->debug($productCollection->getSelectSql(true));
        return $productCollection->getItems();
    }
    public static function getVersion()
    {
        return '2.0.0';
    }
}