<?php

namespace Magezon\Composer\Api\Data;

/**
 * Interface for customer address search results.
 * @api
 * @since 100.0.2
 */
interface PackagesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer addresses list.
     *
     * @return PackagesInterface[]
     */
    public function getItems();

    /**
     * Set customer addresses list.
     *
     * @param PackagesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
