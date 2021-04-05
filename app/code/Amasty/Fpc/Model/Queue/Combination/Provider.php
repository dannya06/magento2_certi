<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


declare(strict_types=1);

namespace Amasty\Fpc\Model\Queue\Combination;

class Provider
{
    /**
     * @var Combinator
     */
    private $combinator;

    /**
     * @var array
     */
    private $combinationSources;

    private $combinations = null;

    public function __construct(
        Combinator $combinator,
        array $combinationSources = []
    ) {
        $this->combinator = $combinator;
        $this->combinationSources = $combinationSources;
    }

    public function getCombinations(): array
    {
        if ($this->combinations === null) {
            $this->combinations = $this->buildCombinations();
        }

        return $this->combinations;
    }

    public function getCombinationSources(): array
    {
        return $this->combinationSources;
    }

    private function buildCombinations(): array
    {
        $combinations = [[]];

        foreach ($this->getCombinationSources() as $combinationSource) {
            $combinations = $this->combinator->execute($combinations, $combinationSource);
        }

        return $combinations;
    }
}
