<?php
/**
 * Edit.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\Packages;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magezon\Composer\Api\PackagesRepositoryInterface;
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
     * @var PackagesRepositoryInterface
     */
    private $packagesRepository;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param PackagesRepositoryInterface $packagesRepository
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        PackagesRepositoryInterface $packagesRepository,
        SessionManagerInterface $sessionManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->packagesRepository = $packagesRepository;
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
                $title = $this->packagesRepository->getById($id)->getName();
            } else {
                $title = __('Add New Package');
            }
        } catch (NoSuchEntityException $e) {
            $title = __('Add New Package');
        }

        $result->getConfig()->getTitle()->prepend($title);
        $result->addBreadcrumb($title, $title);

        return $result;
    }
}
