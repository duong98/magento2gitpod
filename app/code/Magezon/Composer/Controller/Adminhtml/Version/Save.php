<?php
/**
 * Save
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Adminhtml\Version;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magezon\Composer\Api\Data\VersionInterfaceFactory;
use Magezon\Composer\Model\ZipUploader;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    /** @var VersionInterfaceFactory $versionFactory */
    protected $versionFactory;

    /**
     * @var VersionRepositoryInterface
     */
    protected $versionRepository;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ZipUploader
     */
    public $zipUploader;

    /**
     * @param Context $context
     * @param VersionInterfaceFactory $versionFactory
     * @param VersionRepositoryInterface $versionRepository
     * @param LoggerInterface $logger
     * @param ZipUploader $zipUploader
     */
    public function __construct(
        Context $context,
        VersionInterfaceFactory $versionFactory,
        VersionRepositoryInterface $versionRepository,
        LoggerInterface $logger,
        ZipUploader $zipUploader
    ) {
        $this->versionFactory = $versionFactory;
        $this->versionRepository = $versionRepository;
        $this->_logger = $logger;
        $this->zipUploader = $zipUploader;
        parent::__construct($context);
    }

    /**
     * @param array $data
     * @param array $zipData
     * @return array
     */

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
        $zipData = [];
        if (isset($data['zip'][0]['name']) && isset($data['zip'][0]['tmp_name'])) {
            $zipData['name'] = $data['zip'][0]['name'];
            $zipData['type'] = $data['zip'][0]['type'];
            $zipData['size'] = $data['zip'][0]['size'];
            $data['zip'] = json_encode($zipData);
            try {
                $this->zipUploader->moveFileFromTmp((string)$zipData['name'], null);
            } catch(LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } elseif (isset($data['zip'][0]['name']) && !isset($data['zip'][0]['tmp_name'])) {
            $zipData['name'] = $data['zip'][0]['name'];
            $zipData['type'] = $data['zip'][0]['type'];
            $zipData['size'] = $data['zip'][0]['size'];
            $data['zip'] = json_encode($zipData);
            try {
                $this->zipUploader->moveFileFromTmp((string)$zipData['name'], null);
            } catch(LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $data['zip'] = '';
        }
        $versionInstance = $this->versionFactory->create();
        $versionInstance->setData($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        $this->versionRepository->save($versionInstance);
        if ('edit' === $redirectBack) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $versionInstance->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
