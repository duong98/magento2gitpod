<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magezon\Composer\Controller\AccessKeys;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magezon\Composer\Controller\AccessKeys;

/**
 * Disable customer access key controller action.
 */
class Disable extends AccessKeys implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @inheritdoc
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $accessKeyId = $this->getRequest()->getParam('id', false);

        if ($accessKeyId && $this->_formKeyValidator->validate($this->getRequest())) {
            try {
                $accessKey = $this->_accessKeysRepository->getById($accessKeyId);
                if ($accessKey->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $accessKey->setStatus(0);
                    $this->_accessKeysRepository->save($accessKey);
                    $this->messageManager->addSuccessMessage(__('You disabled this key.'));
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t disable the key right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addExceptionMessage($other, __('We can\'t disable the key right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}