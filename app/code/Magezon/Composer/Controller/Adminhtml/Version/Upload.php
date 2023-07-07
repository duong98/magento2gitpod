<?php

namespace Magezon\Composer\Controller\Adminhtml\Version;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magezon\Composer\Model\ZipUploader;
use Magezon\Composer\Helper\Data;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var ZipUploader
     */
    public $zipUploader;

    /**
     * @param Context $context
     * @param ZipUploader $zipUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magezon\Composer\Model\ZipUploader $zipUploader
    ) {
        parent::__construct($context);
        $this->zipUploader = $zipUploader;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magezon_Composer::version');
    }

    public function execute()
    {
        try {
            $result = $this->zipUploader->saveFileToTmpDir('zip');
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}