<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    SocialLogin
 * @version    1.6.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\SocialLogin\Model\Account;

use Aheadworks\SocialLogin\Api\Data\AccountInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Class ImageProvider
 */
class ImageProvider
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var string
     */
    private $placeholderImage;

    /**
     * @param AssetRepository $assetRepository
     * @param string $placeholderImage
     */
    public function __construct(
        AssetRepository $assetRepository,
        $placeholderImage = 'Aheadworks_SocialLogin::images/user_placeholder.png'
    ) {
        $this->assetRepository = $assetRepository;
        $this->placeholderImage = $placeholderImage;
    }

    /**
     * Get account image url.
     *
     * @param AccountInterface $account
     * @return string
     */
    public function getAccountImageUrl(AccountInterface $account)
    {
        $url = $account->getImagePath();

        if (empty($url)) {
            $url = $this->getPlaceholderImageUrl();
        }

        return $url;
    }

    /**
     * Get placeholder image url.
     *
     * @return string
     */
    public function getPlaceholderImageUrl()
    {
        return $this->assetRepository->getUrl($this->placeholderImage);
    }
}
