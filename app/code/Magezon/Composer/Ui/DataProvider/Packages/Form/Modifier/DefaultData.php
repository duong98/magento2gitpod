<?php

declare(strict_types=1);

namespace Magezon\Composer\Ui\DataProvider\Packages\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DefaultData implements ModifierInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * DefaultData constructor.
     *
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $data[null]['product_button_label'] = __('Choose Product');
        $data[null]['product_edit_url'] = $this->urlBuilder->getUrl('catalog/product/edit');
        $data[null]['sku'] = $this->request->getParam('sku');

        foreach ($data as & $eventData) {
            if (empty($eventData['sku'])) {
                $productButtonLabel  = __('Choose Product');
            } else {
                try {
                    $product = $this->productRepository->get($eventData['sku']);
                    $eventData['product_label'] = $product->getName() . ' (' . $product->getSku() . ')';
                    $eventData['product_link']
                        = $this->urlBuilder->getUrl('catalog/product/edit', ['id' => $product->getId()]);
                    $productButtonLabel = __('Change Product');
                } catch (NoSuchEntityException $e) {
                    $eventData['sku'] = null;
                    $productButtonLabel  = __('Choose Product');
                }
            }

            $eventData['product_edit_url'] = $this->urlBuilder->getUrl('catalog/product/edit');
            $eventData['product_button_label'] = $productButtonLabel;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
