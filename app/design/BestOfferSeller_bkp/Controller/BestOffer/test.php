<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Controller\BestOffer;

class test extends \Magento\Framework\App\Action\Action
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

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curlFactory = $curlFactory;
        $this->actionFactory = $actionFactory;

        parent::__construct($context);
    }
    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
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
