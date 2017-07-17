<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard;

use Aheadworks\Giftcard\Model\ResourceModel\Validator\GiftcardIsUnique;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CodeGenerator
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class CodeGenerator
{
    /**
     * @var int
     */
    const CODE_GENERATION_ATTEMPTS = 1000;

    /**
     * @var int
     */
    const CODE_LENGTH = 12;

    /**
     * @var GiftcardIsUnique
     */
    private $giftcardIsUniqueValidator;

    /**
     * @param GiftcardIsUnique $giftcardIsUniqueValidator
     */
    public function __construct(
        GiftcardIsUnique $giftcardIsUniqueValidator
    ) {
        $this->giftcardIsUniqueValidator = $giftcardIsUniqueValidator;
    }

    /**
     * Generate Gift Card code
     *
     * @return string
     * @throws LocalizedException
     */
    public function generate()
    {
        $attempt = 0;
        do {
            if ($attempt >= self::CODE_GENERATION_ATTEMPTS) {
                throw new LocalizedException(__('Unable to create Gift Card code'));
            }
            $code = $this->generateCode();
            $attempt++;
        } while (!$this->giftcardIsUniqueValidator->validate($code));
        return $code;
    }

    /**
     * Generate code
     *
     * @return string
     */
    private function generateCode()
    {
        $code = '';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $strLength = strlen($characters);
        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= $characters[mt_rand(0, $strLength - 1)];
        }
        return $code;
    }
}
