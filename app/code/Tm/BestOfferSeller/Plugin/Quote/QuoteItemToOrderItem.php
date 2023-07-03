<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Plugin\Quote;

use Tm\BestOfferSeller\Setup\UpgradeSchema;

class QuoteItemToOrderItem
{
    private const TBOSO_FIELD_NAME = UpgradeSchema::COLUMN_NAME;

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param $additional
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        $orderItem = $proceed($item, $additional);
        $orderItem->setData(
            self::TBOSO_FIELD_NAME,
            $item->getData(self::TBOSO_FIELD_NAME)
        );


        return $orderItem;
    }
}
