<?php
declare(strict_types=1);
namespace Tm\Provider\Model\Configuration\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Options type calculation
 */
class TypeRateSuccess implements OptionSourceInterface
{

    /**
     * Options key,value
     *
     * @return array
     */
    public function toOptionArray()
    {
        $response = [];
        for ($i = 10; $i <= 100; $i += 10) {
            array_push(
                $response,
                ['value' => $i, 'label' => $i.'%']
            );
        }

        return $response;
    }
}
