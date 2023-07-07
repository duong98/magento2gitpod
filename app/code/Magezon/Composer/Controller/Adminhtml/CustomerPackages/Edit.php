<?php
/**
 * Edit.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\CustomerPackages;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magezon\Composer\Api\CustomerPackagesRepositoryInterface;
use Psr\Log\LoggerInterface;

class Edit extends Action
{

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;
    /**
     * @var CustomerPackagesRepositoryInterface
     */
    private $customerPackagesRepository;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param CustomerPackagesRepositoryInterface $customerPackagesRepository
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        CustomerPackagesRepositoryInterface $customerPackagesRepository,
        SessionManagerInterface $sessionManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerPackagesRepository = $customerPackagesRepository;
        $this->sessionManager = $sessionManager;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Magezon_Composer::accesskeys');
    }

    /**
     * Edit
     *
     * @return Page
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID
        $id = (int) $this->getRequest()->getParam('id');
        $result = $this->resultPageFactory->create();

        try {
            if ($id) {
                $this->sessionManager->setData('id', $id);
                $title = $this->customerPackagesRepository->getById($id)->getName();
            } else {
                $title = __('Add New Customer Package');
            }
        } catch (NoSuchEntityException $e) {
            $title = __('Add New Customer Package');
        }

        $result->getConfig()->getTitle()->prepend($title);
        $result->addBreadcrumb($title, $title);

        return $result;
    }
}
