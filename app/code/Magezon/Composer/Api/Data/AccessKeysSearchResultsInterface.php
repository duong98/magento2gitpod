<?php
namespace Magezon\Composer\Api\Data;

use Magento\Framework\DataObject;

/**
 * Interface for customer address search results.
 * @api
 * @since 100.0.2
 */
interface AccessKeysSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer addresses list.
     *
     * @return DataObject[]
     */
    public function getItems();

    /**
     * Set customer addresses list.
     *
     * @param DataObject[] $items
     * @return $this
     */
    public function setItems(array $items);
}