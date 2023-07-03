<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Controller\BestOffer;
use Magento\Framework\Logger\Monolog;
// MMTodo: Remover endpoint
class mmtest extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curlFactory;

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;
    private \Tm\BestOfferSeller\Model\TmBestOfferSellerOrdersFactory $tmBestOfferSellerOrdersFactory;
    private \Tm\BestOfferSeller\Model\ResourceModel\TmBestOfferSellerOrders $tmBestOfferSellerOrdersResource;
    private \Tm\BestOfferSeller\Console\File\BuildCsv $buildCsv;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Tm\BestOfferSeller\Model\TmBestOfferSellerOrdersFactory $tmBestOfferSellerOrdersFactory,
        \Tm\BestOfferSeller\Model\ResourceModel\TmBestOfferSellerOrders $tmBestOfferSellerOrdersResource,
        Monolog $logger,
        \Tm\BestOfferSeller\Console\File\BuildCsv $buildCsv,
        \Tm\BestOfferSeller\Model\Reports $reports
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curlFactory = $curlFactory;
        $this->actionFactory = $actionFactory;

        parent::__construct($context);
        $this->tmBestOfferSellerOrdersFactory = $tmBestOfferSellerOrdersFactory;
        $this->tmBestOfferSellerOrdersResource = $tmBestOfferSellerOrdersResource;

        $this->logger = $logger;
        $this->buildCsv = $buildCsv;
        $this->reports = $reports;
    }

    public function execute()
    {
        $date = $this->getRequest()->getParam('date');
        $reportDate = $this->reports->reportCsv($date);

        $result = $this->buildCsv->generateCSV($date,$reportDate);
        echo print_r($result,true);die;

//        $data[] = ['sku','type','order_id','createdAt','state','status','bestOfferSeller'];
//
//        foreach ($reportDate as $row){
//            $data[] = [
//                $row['sku'],
//                $row['type'],
//                $row['order_id'],
//                $row['sku'],
//                $row['createdAt'],
//                $row['sku'],
//                $row['state'],
//                $row['status'],
//                json_encode($row['bestOfferSeller']),
//            ];
//        }

        echo print_r($result,true);

//        $this->buildCsv->generateCSV([
//            ['Campo1', 'Campo2', 'Campo3'],
//            ['Valor1', 'Valor2', 'Valor3'],
//            ['Valor4', 'Valor5', 'Valor6'],
//        ]);

        die;
        //$this->logger->info("test",['asd'=> 'as']);
        die;

        $model = $this->tmBestOfferSellerOrdersFactory->create();
        $model->setSku('a')
            ->setOrderId(1)
            ->setOrderItemId(1);
        $this->tmBestOfferSellerOrdersResource->save($model);

        echo print_r($model->getData(),true);


//        $request = $this->getRequest();
//        $request->setParams([
//            'sku' => $this->getRequest()->getParam('sku')
//        ]);
//
//        $forward = $this->actionFactory->create('Tm\Provider\Controller\Provider\Get');
//        return $forward->execute();

//        $result = $this->resultJsonFactory->create();
//        //docker network inspect bridge --format='{{(index .IPAM.Config 0).Gateway}}'
//        $url = 'http://172.17.0.1:80/tm_provider/Provider/All';
//
//        $curl = $this->curlFactory->create();
//        $curl->setOption(CURLOPT_FOLLOWLOCATION, true);
//        $curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
//        $curl->get($url);
//        return $result->setHttpResponseCode(200)->setData($curl->getBody());
//
//        $this->curl->followRedirects(true);
//        $this->curl->get('http://172.17.0.1:80/tm_provider/Provider/All');
//        return $result->setHttpResponseCode(200)->setData($this->curl->getBody());

    }

}
