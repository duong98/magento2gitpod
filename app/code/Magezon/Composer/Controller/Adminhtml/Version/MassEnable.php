<?php
/**
 * MassDelete.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\Version;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Api\Data\VersionInterfaceFactory;
use Magezon\Composer\Model\ResourceModel\Packages\Version\Collection;

/**
 * MassDelete Access Keys
 */
class MassEnable extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /** @var Collection $objectCollection */
    protected $objectCollection;

    /**
     * @var VersionRepositoryInterface
     */
    protected $versionRepository;

    /** @var VersionInterfaceFactory $versionFactory */
    protected $versionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param VersionInterfaceFactory $versionFactory
     * @param VersionRepositoryInterface $versionRepository
     * @param Collection $objectCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        VersionInterfaceFactory $versionFactory,
        VersionRepositoryInterface $versionRepository,
        Collection $objectCollection
    ) {
        $this->filter = $filter;
        $this->objectCollection = $objectCollection;
        $this->versionFactory = $versionFactory;
        $this->versionRepository = $versionRepository;
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
                $accessKey = $this->versionFactory->create();
                $accessKey->setData($item->getData());
                $accessKey->setStatus('1');
                $this->versionRepository->save($accessKey);
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been enabled.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
