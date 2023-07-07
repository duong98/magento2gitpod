<?php
/**
 * Copyright © Nose All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Magezon\Composer\Block\Adminhtml\Packages\Version\Grid;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magezon\Composer\Block\Adminhtml\Packages\Version\GenericButton;

class AddButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Url Builder class
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * CustomButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(Context $context)
    {
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($context);
    }

    /**
     * Set package_id to add button as a parameter
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Add Package Version'),
            'class' => 'retry primary',
            'on_click' =>  "setLocation('"
                . $this->urlBuilder->getUrl('*/*/edit', ['package_id' => $this->getPackageId()])
                . "')"
        ];
    }
}
