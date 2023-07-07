<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magezon\Composer\Controller\Packages;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magezon\Composer\Controller\Packages;

/**
 * Disable customer access key controller action.
 */
class Disable extends Packages implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @inheritdoc
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $packageId = $this->getRequest()->getParam('id', false);

        if ($packageId && $this->_formKeyValidator->validate($this->getRequest())) {
            try {
                $package = $this->_packagesRepository->getById($packageId);
                if ($package->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $package->setStatus(0);
                    $this->_packagesRepository->save($package);
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