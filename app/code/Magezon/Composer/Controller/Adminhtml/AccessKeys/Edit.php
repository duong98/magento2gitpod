<?php
/**
 * Edit.php
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\AccessKeys;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
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
     * @var AccessKeysRepositoryInterface
     */
    private $accessKeysRepository;

    /**
     * @param Context $context
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param AccessKeysRepositoryInterface $accessKeysRepository
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        AccessKeysRepositoryInterface $accessKeysRepository,
        SessionManagerInterface $sessionManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->accessKeysRepository = $accessKeysRepository;
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
                $this->sessionManager->setData('access_id', $id);
                $title = $this->accessKeysRepository->getById($id)->getName();
            } else {
                $title = __('Add New Access Key');
            }
        } catch (NoSuchEntityException $e) {
            $title = __('Add New Access Key');
        }

        $result->getConfig()->getTitle()->prepend($title);
        $result->addBreadcrumb($title, $title);

        return $result;
    }
}
