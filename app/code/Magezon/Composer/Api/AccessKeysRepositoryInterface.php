<?php
namespace Magezon\Composer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magezon\Composer\Api\Data\AccessKeysInterface;
use Magezon\Composer\Api\Data\AccessKeysSearchResultsInterface;

interface AccessKeysRepositoryInterface
{
    /**
     * Get Access key by Id
     *
     * @param int $accessId
     * @return AccessKeysInterface
     */
    public function getById(int $accessId): AccessKeysInterface;

    /**
     * Delete Many Access Keys
     *
     * @param AccessKeysInterface $accessKeys
     * @return bool
     */
    public function delete(AccessKeysInterface $accessKeys): bool;

    /**
     * Save Access Key
     *
     * @param AccessKeysInterface $accessKey
     * @return AccessKeysInterface
     */
    public function save(AccessKeysInterface $accessKey): AccessKeysInterface;

    /**
     * Delete an access key by id
     *
     * @param int $accessId
     * @return bool
     */
    public function deleteById(int $accessId): bool;

    /**
     * Get All Access keys
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria):SearchResults;

    /**
     * Get All Access keys
     *
     * @param string $auth
     * @param string $secret
     * @return SearchResults
     */
    public function getAccessKeysList(string $auth, string $secret): SearchResults;
    /**
     * Get All Access keys
     *
     * @param string $auth
     * @param string $secret
     * @return string
     */
    public function getCustomerId(string $auth, string $secret): string;
}
