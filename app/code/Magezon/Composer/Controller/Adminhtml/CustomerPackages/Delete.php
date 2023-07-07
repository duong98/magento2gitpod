<?php

namespace Magezon\Composer\Controller\Adminhtml\CustomerPackages;

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
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Magezon\Composer\Api\Data\CustomerPackagesInterfaceFactory;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var CustomerPackagesInterfaceFactory
     */
    protected $customerPackagesFactory;

    /**
     * @var CustomerPackagesRepositoryInterface
     */
    protected $customerPackagesRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerPackagesInterfaceFactory $customerPackagesFactory
     * @param CustomerPackagesRepositoryInterface $customerPackagesRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerPackagesInterfaceFactory $customerPackagesFactory,
        CustomerPackagesRepositoryInterface $customerPackagesRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerPackagesFactory = $customerPackagesFactory;
        $this->customerPackagesRepository = $customerPackagesRepository;
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
            $this->customerPackagesRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('The key has been deleted.'));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t delete key.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t find a key to duplicate.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
