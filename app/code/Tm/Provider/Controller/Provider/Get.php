<?php declare(strict_types=1);

namespace Tm\Provider\Controller\Provider;

class Get extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Tm\Provider\Model\ResponseEndpoint
     */
    private $responseEndpoint;

    /**
     * @var \Tm\Provider\Model\DataBase
     */
    private $randomSuccess;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Tm\Provider\Model\ResponseEndpoint $responseEndpoint
     * @param \Tm\Provider\Model\DataBase $randomSuccess
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Tm\Provider\Model\ResponseEndpoint $responseEndpoint,
        \Tm\Provider\Model\DataBase $randomSuccess
    ) {
        $this->responseEndpoint = $responseEndpoint;
        $this->randomSuccess= $randomSuccess->getDataBase();

        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku');

        if(!$this->randomSuccess['result']){
            return $this->response(400);
        }

        $dataBase = $this->randomSuccess['database'] ?? null;
        if($dataBase === null){
            return $this->response(500);
        }

        $dataBase = $this->filterBySKU($dataBase, $sku);
        if(!isset($dataBase) && $dataBase === null){
            return $this->response(202);
        }

        return $this->response(200);
    }

    /**
     * @param $data
     * @param $sku
     * @return array|null
     */
    private function filterBySKU($data, $sku)
    {
        $allSkus = array_column($data, 'sku');
        if(!in_array($sku, $allSkus)){
            return null;
        }

        $filteredData = array_filter($data, function ($item) use ($sku) {
            return $item['sku'] === $sku;
        });

        return array_values($filteredData);
    }

    /**
     * @param int $code
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function response(int $code){
        return $this->responseEndpoint->formatResponse(
            $code,
            $this->randomSuccess
        );
    }
}
