<?php
/**
 * Save
 *
 * @copyright Copyright © ${commentsYear} ${CommentsCompanyName}. All rights reserved.
 * @author    ${commentsUserEmail}
 */
namespace Magezon\Composer\Controller\Packages;

use Cassandra\Exception\AlreadyExistsException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magezon\Composer\Controller\Packages;

class Save extends Packages implements HttpPostActionInterface
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
        $packageInstance = $this->_packagesFactory->create();
        $packageInstance->setData($data);
        $packageInstance->setCustomerId($customerId);
        $packageInstance->setStatus(1);
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        try {
            $this->_packagesRepository->save($packageInstance);
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
