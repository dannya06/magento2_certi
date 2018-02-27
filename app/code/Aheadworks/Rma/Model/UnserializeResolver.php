<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model;

/**
 * Class UnserializeResolver
 *
 * @package Aheadworks\Rma\Model
 */
class UnserializeResolver
{
    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function unserialize($string)
    {
        $result = $this->unserializeString($string);
        return $result === false ? $this->jsonDecodeString($string) : $result;
    }

    /**
     * Unserialize string with unserialize method
     *
     * @param $string
     * @return array|bool
     */
    private function unserializeString($string)
    {
        $result = @unserialize($string);

        if ($result !== false || $string === 'b:0;') {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Unserialize string with json_decode method
     *
     * @param $string
     * @return array
     */
    private function jsonDecodeString($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }
}
