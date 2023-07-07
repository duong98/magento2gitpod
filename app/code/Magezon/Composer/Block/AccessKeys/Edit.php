<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Composer
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

namespace Magezon\Composer\Block\AccessKeys;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class Edit extends Template
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var SessionFactory
     */
    private $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param SessionFactory $customerSession
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SessionFactory $customerSession,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->_logger = $logger;

        parent::__construct($context, $data);
    }

    /**
     * Get current customer
     *
     * Return stored customer or get it from session
     *
     * @since 102.0.1
     */
    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if ($customer === null) {
            $customer = $this->customerSession->create()->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * Generate and return "New Access Key" URL
     *
     * @return string
     * @since 102.0.1
     */
    public function getSaveUrl(): string
    {
        return $this->getUrl('composer/accesskeys/save', ['_secure' => true]);
    }
}
