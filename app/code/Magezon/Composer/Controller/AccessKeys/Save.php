<?php
/**
 * Save
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\AccessKeys;

use Cassandra\Exception\AlreadyExistsException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magezon\Composer\Controller\AccessKeys;

class Save extends AccessKeys implements HttpPostActionInterface
{

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $this->_logger->debug('erererere');
        $this->_logger->debug(json_encode($data));
        $customerId = $this->_getSession()->getCustomerId();
        $accessKeyInstance = $this->_accessKeysFactory->create();
        $accessKeyInstance->setData($data);
        $accessKeyInstance->setCustomerId($customerId);
        $accessKeyInstance->setStatus(1);
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        try {
            $this->_accessKeysRepository->save($accessKeyInstance);
            $this->messageManager->addSuccessMessage(__('Key was successfully created.'));
        } catch (AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage(__("Key Name already exists."));
            $resultRedirect->setPath('*/*/index');
            if ('new' === $redirectBack) {
                $resultRedirect->setPath('*/*/new');
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
