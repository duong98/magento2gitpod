<?php
/**
 * MassDelete.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\Packages;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Api\Data\PackagesInterfaceFactory;
use Magezon\Composer\Model\ResourceModel\Packages\Collection;

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
    protected $packagesRepository;

    /** @var PackagesInterfaceFactory $packagesFactory */
    protected $packagesFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param PackagesInterfaceFactory $packagesFactory
     * @param PackagesRepositoryInterface $packagesRepository
     * @param Collection $objectCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        PackagesInterfaceFactory $packagesFactory,
        PackagesRepositoryInterface $packagesRepository,
        Collection $objectCollection
    ) {
        $this->filter = $filter;
        $this->objectCollection = $objectCollection;
        $this->packagesFactory = $packagesFactory;
        $this->packagesRepository = $packagesRepository;
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
                $accessKey = $this->packagesFactory->create();
                $accessKey->setData($item->getData());
                $this->packagesRepository->delete($accessKey);
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
