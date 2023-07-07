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
 * Enable customer access key controller action.
 */
class Enable extends AccessKeys implements HttpPostActionInterface, HttpGetActionInterface
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
                    $accessKey->setStatus(1);
                    $this->_accessKeysRepository->save($accessKey);
                    $this->messageManager->addSuccessMessage(__('You enabled this key.'));
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t enable the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addExceptionMessage($other, __('We can\'t enable the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}