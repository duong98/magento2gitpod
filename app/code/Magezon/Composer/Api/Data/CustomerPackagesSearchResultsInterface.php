<?php

namespace Magezon\Composer\Api\Data;

/**
 * Interface for customer address search results.
 * @api
 * @since 100.0.2
 */
interface CustomerPackagesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer addresses list.
     *
     * @return CustomerPackagesInterface[]
     */
    public function getItems();

    /**
     * Set customer packages list.
     *
     * @param CustomerPackagesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
