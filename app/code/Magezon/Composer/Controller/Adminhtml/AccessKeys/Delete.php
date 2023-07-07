<?php

namespace Magezon\Composer\Controller\Adminhtml\AccessKeys;

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
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\Data\AccessKeysInterface;
use Magezon\Composer\Api\Data\AccessKeysInterfaceFactory;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var AccessKeysInterfaceFactory
     */
    protected $accessKeysFactory;

    /**
     * @var AccessKeysRepositoryInterface
     */
    protected $accessKeysRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AccessKeysInterfaceFactory $accessKeysFactory
     * @param AccessKeysRepositoryInterface $accessKeysRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AccessKeysInterfaceFactory $accessKeysFactory,
        AccessKeysRepositoryInterface $accessKeysRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->accessKeysFactory = $accessKeysFactory;
        $this->accessKeysRepository = $accessKeysRepository;
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
            $this->accessKeysRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('The key has been deleted.'));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t delete key.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('We can\'t find a key to duplicate.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
