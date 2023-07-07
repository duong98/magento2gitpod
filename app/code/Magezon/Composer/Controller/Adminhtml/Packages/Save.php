<?php
/**
 * Save
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\Packages;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magezon\Composer\Api\Data\PackagesInterfaceFactory;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    /** @var PackagesInterfaceFactory $packagesFactory */
    protected $packagesFactory;

    /**
     * @var PackagesRepositoryInterface
     */
    protected $packagesRepository;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param Context $context
     * @param PackagesInterfaceFactory $packagesFactory
     * @param PackagesRepositoryInterface $packagesRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PackagesInterfaceFactory $packagesFactory,
        PackagesRepositoryInterface $packagesRepository,
        LoggerInterface $logger
    ) {
        $this->packagesFactory = $packagesFactory;
        $this->packagesRepository = $packagesRepository;
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
        $packageInstance = $this->packagesFactory->create();
        $packageInstance->setData($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        $this->packagesRepository->save($packageInstance);
        if ('edit' === $redirectBack) {
            $resultRedirect->setPath('*/*/edit', ['id' => $packageInstance->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
