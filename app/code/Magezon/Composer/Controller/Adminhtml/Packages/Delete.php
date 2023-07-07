<?php

namespace Magezon\Composer\Controller\Adminhtml\Packages;

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
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Api\Data\PackagesInterface;
use Magezon\Composer\Api\Data\PackagesInterfaceFactory;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var PackagesInterfaceFactory
     */
    protected $packagesFactory;

    /**
     * @var PackagesRepositoryInterface
     */
    protected $packagesRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PackagesInterfaceFactory $packagesFactory
     * @param PackagesRepositoryInterface $packagesRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PackagesInterfaceFactory $packagesFactory,
        PackagesRepositoryInterface $packagesRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->packagesFactory = $packagesFactory;
        $this->packagesRepository = $packagesRepository;
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
            $this->packagesRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('The key has been deleted.'));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t delete key.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t find a key to duplicate.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
