<?php
namespace Aheadworks\Blog\Model;

/**
 * Disqus model
 * @package Aheadworks\Blog\Model
 */
class Disqus
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Disqus\Api
     */
    protected $disqusApi;

    /**
     * Configured forum codes cache
     *
     * @var array|null
     */
    protected $configForumCodes = null;

    /**
     * Comments data cache
     *
     * @var array
     */
    protected $commentsData = [];

    /**
     * Disqus constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Config $config
     * @param Disqus\Api $disqusApi
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Config $config,
        Disqus\Api $disqusApi
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->disqusApi = $disqusApi;
    }

    /**
     * Retrieves Disqus admin url
     *
     * @return string
     */
    public function getAdminUrl()
    {
        $forumCodePart = $this->getForumCode() ? $this->getForumCode() . '.' : '';
        return "https://" . $forumCodePart . "disqus.com/admin/moderate";
    }

    /**
     * Retrieves number of published comments
     *
     * @param  mixed $id
     * @return int
     * @codeCoverageIgnore
     */
    public function getPublishedCommentsNum($id)
    {
        if (!isset($this->commentsData[$id])) {
            $this->commentsData[$id] = $this->getCommentsData($id);
        }
        return $this->commentsData[$id]['published'];
    }

    /**
     * Retrieves number of new comments
     *
     * @param  mixed $id
     * @return int
     * @codeCoverageIgnore
     */
    public function getNewCommentsNum($id)
    {
        if (!isset($this->commentsData[$id])) {
            $this->commentsData[$id] = $this->getCommentsData($id);
        }
        return $this->commentsData[$id]['new'];
    }

    /**
     * Retrieves forum code option
     *
     * @param  int|null $storeId
     * @param  int|null $websiteId
     * @return mixed
     */
    protected function getForumCode($storeId = null, $websiteId = null)
    {
        return $this->config->getValue(
            Config::XML_GENERAL_DISQUS_FORUM_CODE,
            $storeId,
            $websiteId
        );
    }

    /**
     * Retrieves all configured forum codes
     *
     * @return array|null
     */
    protected function getConfigForumCodes()
    {
        if ($this->configForumCodes === null) {
            $this->configForumCodes = [];
            foreach ($this->storeManager->getWebsites() as $website) {
                $forumCode = $this->getForumCode(null, $website->getId());
                if (!in_array($forumCode, $this->configForumCodes)) {
                    $this->configForumCodes[] = $forumCode;
                }
            }
        }
        return $this->configForumCodes;
    }

    /**
     * Retrieves comments data using Disqus API
     *
     * @param  mixed $id
     * @return array
     * @codeCoverageIgnore
     */
    protected function getCommentsData($id)
    {
        $published = 0;
        $new = 0;
        foreach ($this->getConfigForumCodes() as $forumCode) {
            $commentsData = $this->disqusApi->sendRequest(
                'threads/listPosts',
                [
                    'forum' => $forumCode,
                    'thread:ident' => $id,
                    'related' => ['thread'],
                    'include' => ['unapproved', 'approved']
                ]
            );
            if (is_array($commentsData)) {
                foreach ($commentsData as $commentData) {
                    if ($commentData['isApproved']) {
                        $published++;
                    } else {
                        $new++;
                    }
                }
            }
        }
        return ['published' => $published, 'new' => $new];
    }
}
