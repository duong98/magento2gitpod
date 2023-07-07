<?php
/**
 * MassDelete.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\AccessKeys;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\Data\AccessKeysInterfaceFactory;
use Magezon\Composer\Model\ResourceModel\AccessKeys\Collection;

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
     * @var AccessKeysRepositoryInterface
     */
    protected $accessKeysRepository;

    /** @var AccessKeysInterfaceFactory $accessKeysFactory */
    protected $accessKeysFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param AccessKeysInterfaceFactory $accessKeysFactory
     * @param AccessKeysRepositoryInterface $accessKeysRepository
     * @param Collection $objectCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        AccessKeysInterfaceFactory $accessKeysFactory,
        AccessKeysRepositoryInterface $accessKeysRepository,
        Collection $objectCollection
    ) {
        $this->filter = $filter;
        $this->objectCollection = $objectCollection;
        $this->accessKeysFactory = $accessKeysFactory;
        $this->accessKeysRepository = $accessKeysRepository;
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
                $accessKey = $this->accessKeysFactory->create();
                $accessKey->setData($item->getData());
                $this->accessKeysRepository->delete($accessKey);
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
