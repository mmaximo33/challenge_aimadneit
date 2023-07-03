<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Observer\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Tm\BestOfferSeller\Setup\UpgradeSchema;

class AddToCart implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $columnTmBestOfferSeller = UpgradeSchema::COLUMN_NAME;
        $item = $observer->getEvent()->getData('quote_item');
        $product = $item->getProduct();

        $item->setData(
            $columnTmBestOfferSeller,
            json_encode(
                $product->getData($columnTmBestOfferSeller)
            )
        );

        return $this;
    }
}
