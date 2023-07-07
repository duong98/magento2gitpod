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
 * Enable customer access key controller action.
 */
class Enable extends Packages implements HttpPostActionInterface, HttpGetActionInterface
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
                    $package->setStatus(1);
                    $this->_packagesRepository->save($package);
                    $this->messageManager->addSuccessMessage(__('You enabled this key.'));
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t enable the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('We can\'t enable the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}