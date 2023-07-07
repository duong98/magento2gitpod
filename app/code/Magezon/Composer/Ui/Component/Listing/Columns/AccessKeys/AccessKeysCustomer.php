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

declare(strict_types=1);

namespace Magezon\Composer\Ui\Component\Listing\Columns\AccessKeys;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class AccessKeysCustomer extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * CategoryEvent constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $currentField = $this->getData('name');

            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$currentField]) && $item[$currentField]) {
                    $item['customerLink']
                        = $this->urlBuilder->getUrl('customer/index/edit', ['id' => $item[$currentField]]);
                } else {
                    unset($item[$currentField]);
                }
            }
        }

        return $dataSource;
    }
}
