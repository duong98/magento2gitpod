<?php
/**
 * Copyright © Nose All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Magezon\Composer\Block\Adminhtml\Packages\Version\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magezon\Composer\Block\Adminhtml\Packages\Version\GenericButton;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Url Builder var
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
     * Set order_id to save button as a parameter
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'on_click' => '',
            'sort_order' => 20,
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'composer_version_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    ['package_id' => $this->getPackageId()],
                                ]
                            ]
                        ]
                    ]
                ],

            ]
        ];
    }
}
