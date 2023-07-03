<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Plugin\Catalog;

use Tm\BestOfferSeller\Model\BestOfferFinder;
use Tm\BestOfferSeller\Setup\UpgradeSchema;

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
    public function afterGetPrice(
        \Magento\Catalog\Model\Product $subject,
        $result
    )
    {
        try {
            $hasOffer = $this->bestOfferFinder->getBestOffer($subject->getSku(),$result);
            if($hasOffer === null ){
                return $result;
            }

            // MMTodo: undefined
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
