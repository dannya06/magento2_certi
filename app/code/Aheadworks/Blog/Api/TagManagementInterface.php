<?php
namespace Aheadworks\Blog\Api;

/**
 * Tag management interface.
 *
 * @api
 */
interface TagManagementInterface
{
    /**
     * Lists of tags for a cloud widget.
     *
     * @param int $storeId
     * @param int|null $categoryId
     * @return \Aheadworks\Blog\Api\Data\TagSearchResultsInterface
     */
    public function getCloudTags($storeId, $categoryId = null);
}
