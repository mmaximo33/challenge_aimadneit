<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Observer\Checkout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Tm\BestOfferSeller\Setup\UpgradeSchema;

class CheckoutSetPriceItem implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();

        $bestOfferSeller = $product->getData(UpgradeSchema::COLUMN_NAME);
        if(!empty($bestOfferSeller)){
            $newPrice = $bestOfferSeller['offerSellerPrice'];
            if (!empty($newPrice)) {
                $quoteItem->setCustomPrice($newPrice);
                $quoteItem->setOriginalCustomPrice($newPrice);
                $quoteItem->getProduct()->setIsSuperMode(true);
            }
        }

        return $this;
    }
}
