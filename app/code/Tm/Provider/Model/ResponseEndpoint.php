<?php declare(strict_types=1);

namespace Tm\Provider\Model;

/**
 * Random Success
 */
class ResponseEndpoint
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $result;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->result = $resultJsonFactory->create();
    }

    /**
     * @param int $code
     * @param array $data
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function formatResponse(int $code, array $data)
    {
        $response['result'] = $data['result'];
        $response['code'] = $code;
        $response['message'] = __(sprintf('tm_provider_endpoint_%s', $code));

        if(in_array($code,[200,202,400])) {
            $response['successRate'] = sprintf("%s%%", $data['rate']);
        }

        if($code === 200){
            $response['data'] = $data['database'];
        }

        return $this->result
            ->setHttpResponseCode($code)
            ->setData($response);
    }
}
