<?php
/**
 * Copyright © Nose All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Magezon\Composer\Block\Adminhtml\Packages\Version;

use Magento\Backend\Block\Widget\Context;

abstract class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * GenericButton constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Get order id from the URL
     *
     * @return mixed
     */
    public function getPackageId()
    {
        return $this->context->getRequest()->getParam('package_id');
    }
}
