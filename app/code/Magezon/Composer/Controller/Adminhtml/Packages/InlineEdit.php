<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magezon\Composer\Controller\Adminhtml\Packages;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magezon\Composer\Model\ResourceModel\Packages\Collection as PackagesCollection;
use Psr\Log\LoggerInterface;

class InlineEdit extends Action
{

    /** @var JsonFactory $jsonFactory */
    protected $jsonFactory;
    /**
     * logging Variable
     *
     * @var LoggerInterface [type]
     */
    protected $_logger;

    /**
     * @var PackagesCollection
     */
    protected $objectCollection;

    /**
     * @param LoggerInterface $logger
     * @param Context $context
     * @param PackagesCollection $objectCollection
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        LoggerInterface      $logger,
        Context              $context,
        PackagesCollection $objectCollection,
        JsonFactory          $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->objectCollection = $objectCollection;
        $this->_logger = $logger;
    }

    /**
     * Result or Redirect
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        $this->_logger->debug('Foo: ', $postItems);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        try {
            $this->objectCollection
                ->addFieldToFilter('id', ['in' => array_keys($postItems)])
                ->walk('saveCollection', [$postItems]);
        } catch (Exception $e) {
            $messages[] = __('There was an error saving the data: ') . $e->getMessage();
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
