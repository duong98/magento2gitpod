<?php

namespace Magezon\Composer\Controller\Adminhtml\Version;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Api\Data\VersionInterface;
use Magezon\Composer\Api\Data\VersionInterfaceFactory;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var VersionInterfaceFactory
     */
    protected $versionFactory;

    /**
     * @var VersionRepositoryInterface
     */
    protected $versionRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param VersionInterfaceFactory $versionFactory
     * @param VersionRepositoryInterface $versionRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        VersionInterfaceFactory $versionFactory,
        VersionRepositoryInterface $versionRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->versionFactory = $versionFactory;
        $this->versionRepository = $versionRepository;
        parent::__construct($context);
    }

    /**
     * Delete item
     *
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $this->versionRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('The key has been deleted.'));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t delete version.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t find a version to duplicate.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
