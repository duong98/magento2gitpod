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
 * Delete customer address controller action.
 */
class Delete extends AccessKeys implements HttpPostActionInterface, HttpGetActionInterface
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
                    $this->_accessKeysRepository->deleteById($accessKeyId);
                    $this->messageManager->addSuccessMessage(__('You deleted this key.'));
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t delete the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addExceptionMessage($other, __('We can\'t delete the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}