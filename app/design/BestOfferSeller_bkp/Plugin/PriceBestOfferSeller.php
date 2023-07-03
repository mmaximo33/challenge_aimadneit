<?php
namespace Tm\BestOfferSeller\Plugin;

use mysql_xdevapi\Exception;
use Tm\BestOfferSeller\Setup\UpgradeSchema;
use Tm\BestOfferSeller\Model\BestOfferFinder;

/*
 * PriceBestOfferSeller
 */
class PriceBestOfferSeller
{
    /**
     * @var Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var Tm\BestOfferSeller\Model\BestOfferFinder
     */
    private $bestOfferFinder;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param BestOfferFinder $bestOfferFinder
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Tm\BestOfferSeller\Model\BestOfferFinder $bestOfferFinder
    )
    {
        $this->actionFactory = $actionFactory;
        $this->bestOfferFinder = $bestOfferFinder;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return float|int|mixed
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        try {
            $hasOffer = $this->bestOfferFinder->getBestOffer($subject->getSku(),$subject->getPrice());

            if($hasOffer === null ){
                return $result;
            }
            $newPrice = $hasOffer['price'] + $hasOffer['shipping_price'];
            $subject->setData(UpgradeSchema::COLUMN_NAME, [
                'originalPrice' => $result,
                'offerSellerPrice' => $newPrice,
                'dataBestOfferSeller' => $hasOffer
            ]);
        }catch (Exception $e){
            return $result;
        }

        return $newPrice;
    }
}
