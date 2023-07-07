<?php
/**
 * Edit.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\Version;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Helper\Data;
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
     * @var VersionRepositoryInterface
     */
    private $versionRepository;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param VersionRepositoryInterface $versionRepository
     * @param SessionManagerInterface $sessionManager
     * @param Data $dataHelper
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        VersionRepositoryInterface $versionRepository,
        SessionManagerInterface $sessionManager,
        Data $dataHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->versionRepository = $versionRepository;
        $this->sessionManager = $sessionManager;
        $this->_logger = $logger;
        $this->dataHelper = $dataHelper;
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
        $this->messageManager->addNoticeMessage(
            $this->dataHelper->getMaxUploadSizeMessage()
        );
        // 1. Get ID
        $id = (int) $this->getRequest()->getParam('id');
        $result = $this->resultPageFactory->create();

        try {
            if ($id) {
                $this->sessionManager->setData('id', $id);
                $title = $this->versionRepository->getById($id)->getName();
            } else {
                $title = __('Add New Package Version');
            }
        } catch (NoSuchEntityException $e) {
            $title = __('Add New Package Version');
        }

        $result->getConfig()->getTitle()->prepend($title);
        $result->addBreadcrumb($title, $title);

        return $result;
    }
}
