<?php
/**
 * MassDelete.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\CustomerPackages;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Magezon\Composer\Api\Data\CustomerPackagesInterfaceFactory;
use Magezon\Composer\Model\ResourceModel\CustomerPackages\Collection;

/**
 * MassDelete Access Keys
 */
class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /** @var Collection $objectCollection */
    protected $objectCollection;

    /**
     * @var PackagesRepositoryInterface
     */
    protected $customerPackagesRepository;

    /** @var CustomerPackagesInterfaceFactory $customerPackagesFactory */
    protected $customerPackagesFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CustomerPackagesInterfaceFactory $customerPackagesFactory
     * @param CustomerPackagesRepositoryInterface $customerPackagesRepository
     * @param Collection $objectCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CustomerPackagesInterfaceFactory $customerPackagesFactory,
        CustomerPackagesRepositoryInterface $customerPackagesRepository,
        Collection $objectCollection
    ) {
        $this->filter = $filter;
        $this->objectCollection = $objectCollection;
        $this->customerPackagesFactory = $customerPackagesFactory;
        $this->customerPackagesRepository = $customerPackagesRepository;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->objectCollection);
        $collectionSize = $collection->getSize();
        if ($collectionSize > 0) {
            foreach ($collection as $item) {
                $accessKey = $this->customerPackagesFactory->create();
                $accessKey->setData($item->getData());
                $this->customerPackagesRepository->delete($accessKey);
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
