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

namespace Magezon\Composer\Ui\Component\Listing\Columns\Packages\Version;

use Magento\Framework\Url;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Psr\Log\LoggerInterface;

class Action extends Column
{

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var string
     */
    protected $_viewUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param string $viewUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        $viewUrl = '',
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_viewUrl    = $viewUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'access_id';
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$urlEntityParamName])) {
                    $viewUrlPath = $this->getData('config/viewUrlPath') ?: 'composer/version/edit';
                    $deleteUrlPath = 'composer/version/delete';
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->_urlBuilder->getUrl(
                                $viewUrlPath,
                                [
                                    'id' => $item[$urlEntityParamName],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->_urlBuilder->getUrl(
                                $deleteUrlPath,
                                [
                                    'id' => $item[$urlEntityParamName],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete %1 ?',$item['version']),
                                'message' => __('Are you sure you want to delete version %1 of package %2 ?',$item['version'],$item['package_id']),
                            ],
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
