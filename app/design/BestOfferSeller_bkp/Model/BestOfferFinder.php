<?php
namespace Tm\BestOfferSeller\Model;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ActionFactory;

use Tm\Provider\Controller\Provider\Get AS EndpointSeller;
class BestOfferFinder
{

    /**
     * @var Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    private $productOriginalPrice;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory
    )
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * get best offer
     * @param $sku
     * @param $price
     * @return array|null
     */
    public function getBestOffer($sku,$price)
    {
        $this->productOriginalPrice = $price;
        return $this->calculateOfferScore($sku);
    }

    /**
     * Calculate offer scores
     * @param $sku
     * @return float|int
     */
    private function calculateOfferScore($sku)
    {
        try {
            $offers = $this->getDataEndpointSeller($sku);

            if($offers === null){
                return null;
            }

            $elementsToEvaluate = $this->prepareElementToEvaluate($offers);
            $scorePerElements = $this->scorePerElements($elementsToEvaluate);
            $bestOfferId = $this->getBestOfferId($scorePerElements);
        } catch (\Exception $e) {
            return null;
        }

        return $this->getBestOfferData($offers, $bestOfferId);
    }


    /**
     * Target $elementsToEvaluate
     *
     *  {
     *      "price": { "x10": 75, "x11": 50 },
     *      "stock": { "x10": 100, "x11": 60 },
     *      "qualification": { "x10": 5, "x11": 2 },
     *      "reviews_quantity": { "x10": 1, "x11": 100 }
     *  }
     *
     * @return array
     */
    private function prepareElementToEvaluate($offers){
        $elementsToEvaluate = [];
        foreach ($offers as $offer) {
            // MMTodo: Analizar calculo en funcion de reportes
            // Argument: priceProduct no contempla priceShippingProduct(Marketplace), con lo cual esto puede ser un error.
            // El calculo de score price, se realiza sobre la diferencia del precio original - oferta
            // price	offer	diff	weight	Score
            // 100	    20	    80	    0.2	    16
            // 100	    50	    50	    0.2	    10
            $priceTotal = $offer['price'] + $offer['shipping_price'];
            $priceDiff = $this->productOriginalPrice - $priceTotal;

            $xId                                            = sprintf('x%s', $offer['id']);
            $elementsToEvaluate['price'][$xId]              = $priceDiff;
            $elementsToEvaluate['stock'][$xId]              = $offer['stock'];
            $elementsToEvaluate['qualification'][$xId]      = $offer['seller']['qualification'];
            $elementsToEvaluate['reviews_quantity'][$xId]   = $offer['seller']['reviews_quantity'];
        }

        return $elementsToEvaluate;
    }

    /**
     * Target Best value, highest score of total offers
     *
     *  {
     *      "x10": {
     *          "price": 2,
     *          "stock": 2,
     *          "qualification": 2,
     *          "reviews_quantity": 1
     *      },
     *      "x11": {
     *          "price": 1,
     *          "stock": 1,
     *          "qualification": 1,
     *          "reviews_quantity": 2
     *      },
     *  }
     * @param $elementsToEvaluate
     * @return array
     */
    private function scorePerElements($elementsToEvaluate)
    {
        $scorePerElements = [];
        $score = $scoreDefault = count($elementsToEvaluate);
        foreach ($elementsToEvaluate as $element => $elementV) {
            $getValues=[];
            $getValues = array_merge($getValues, array_values($elementsToEvaluate[$element]));

            // MMTodo: Very Very important
            rsort($getValues);

            foreach ($getValues as $value){
                $key = array_search($value,$elementsToEvaluate[$element],true);
                $scorePerElements[$key][$element] = $score;
                $score = $score -1;
            }
            $score =  $scoreDefault;
        }
        return $scorePerElements;
    }


    /**
     * Assign weight
     *
     *  {
     *      "x10": 1.8, //Best offer!
     *      "x11": 1.2
     *  }
     * @param $scorePerElements
     * @return int
     */
    private function getBestOfferId($scorePerElements){
        $weightPrice = 0.4; $weightQualification = 0.3; $weightReviews = 0.2; $weightStock = 0.1;

        $bestOfferId = null;
        $bestOfferScore = null;
        $totalScoreOffers = [];
        foreach ($scorePerElements as $key => $scores){
            $scorePrice         = $scores['price'] * $weightPrice;
            $scoreQualification = $scores['qualification'] * $weightQualification;
            $scoreReviews       = $scores['reviews_quantity'] * $weightReviews;
            $scoreStock         = $scores['stock'] * $weightStock;

            $offerScore = $scorePrice + $scoreQualification + $scoreReviews + $scoreStock;
            $totalScoreOffers[$key] = $offerScore;

            if ($bestOfferId === null || $offerScore < $bestOfferScore) {
                $bestOfferId = str_replace('x','',$key);
            }
        }

        return $bestOfferId;
        return $$totalScoreOffers; // debug
    }

    /**
     *
     *  {
     *      "id": 11,
     *      "price": 10,
     *      "shipping_price": 20,
     *      "stock": 60,
     *      "delivery_date": "2023-10-01",
     *      "can_be_refunded": true,
     *      "status": "new",
     *      "guarantee": true,
     *      "seller": {
     *          "name": "Provider-02",
     *          "qualification": 2,
     *          "reviews_quantity": 100
     *      }
     *  }
     * @param $offers
     * @param $bestOfferId
     * @return mixed
     */
    private function getBestOfferData($offers, $bestOfferId){
        $indexOffer = array_search($bestOfferId, array_column($offers, 'id'));
        return $offers[$indexOffer];

    }

    /**
     * Simulate curl to endpoint seller
     *
     * @param string $sku
     */
    private function getDataEndpointSeller(string $sku)
    {
        //MMTodo: Exception rapida
        return $this->test($sku);

        $forward = $this->actionFactory->create(EndpointSeller::class);
        $forward->getRequest()->setParams(['sku' => $sku]);
        $result = $forward->execute();
        return $result;
    }

    private function test($sku){
        return null;
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $database = $objectManager->create(\Tm\Provider\Model\DataBase::class);
//        $data = $database->getDataBase()['database'];
//
//        $allSkus = array_column($data, 'sku');
//        if(!in_array($sku, $allSkus)){
//            return null;
//        }
//
//        $filteredData = array_filter($data, function ($item) use ($sku) {
//            return $item['sku'] === $sku;
//        });
//
//        $response = array_values($filteredData);
//        return $response[0]['offers'];
    }

}
