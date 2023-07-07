<?php

namespace Magezon\Composer\Api\Data;

/**
 * Interface for customer address search results.
 * @api
 * @since 100.0.2
 */
interface VersionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer addresses list.
     *
     * @return VersionInterface[]
     */
    public function getItems();

    /**
     * Set customer addresses list.
     *
     * @param VersionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
