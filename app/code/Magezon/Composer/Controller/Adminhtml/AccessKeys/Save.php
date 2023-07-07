<?php
/**
 * Save
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\AccessKeys;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\Data\AccessKeysInterfaceFactory;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    /** @var AccessKeysInterfaceFactory $accessKeysFactory */
    protected $accessKeysFactory;

    /**
     * @var AccessKeysRepositoryInterface
     */
    protected $accessKeysRepository;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param Context $context
     * @param AccessKeysInterfaceFactory $accessKeysFactory
     * @param AccessKeysRepositoryInterface $accessKeysRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        AccessKeysInterfaceFactory $accessKeysFactory,
        AccessKeysRepositoryInterface $accessKeysRepository,
        LoggerInterface $logger
    ) {
        $this->accessKeysFactory = $accessKeysFactory;
        $this->accessKeysRepository = $accessKeysRepository;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magezon_Composer::accesskeys');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $accessKeyInstance = $this->accessKeysFactory->create();
        $accessKeyInstance->setData($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        $this->accessKeysRepository->save($accessKeyInstance);
        if ('edit' === $redirectBack) {
            $resultRedirect->setPath('*/*/edit', ['id' => $accessKeyInstance->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
