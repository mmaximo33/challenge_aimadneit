<?php declare(strict_types=1);
namespace Tm\Provider\Model;

use Magento\Framework\Exception\FileSystemException;
use Tm\Provider\Model;
class DataBase
{
    private const DATABASE_FILE = "/database.json";

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * @var integer
     */
    private $rate;

    /**
     * @var bool
     */
    private $debugMode;

    /**
     * Construct
     *
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Tm\Provider\Model\Configuration\Config $config
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Tm\Provider\Model\Configuration\Config $config
    ) {
        $this->fileDriver = $fileDriver;
        $this->rate = $config->getSuccessRate();
    }

    public function getDataBase(bool $debugMode = false){
        $this->debugMode = $debugMode;

        $result = $this->probability();
        if($result['result'] || $this->debugMode){
            $result['database'] = $this->importDataBase();
        }

        return $result;
    }

    /**
     * Simulate fail provider endpoint
     *
     * @return bool
     */
    public function probability()
    {
        $probability = $this->rate;
        $randomNumber = rand(1, 100);

        return [
            'result' => ($randomNumber <= $probability),
            'rate' => $probability
        ];
    }

    /**
     * Simulate providers endpoints
     *
     * @return mixed|null
     * @throws FileSystemException
     */
    private function importDataBase(){
        $filePath = dirname(__FILE__) . self::DATABASE_FILE;
        if ($this->fileDriver->isExists($filePath)) {
            $content = $this->fileDriver->fileGetContents($filePath);
            return json_decode($content, true);
        }else{
            return null;
        }
    }
}
