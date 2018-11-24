<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 22:18
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Database;

use nguyenanhung\MantisBT\MantisConnector;
use nguyenanhung\MyCache\Cache;
use nguyenanhung\MyDatabase\Model\BaseModel;
use nguyenanhung\MyDebug\Benchmark;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\VnTelcoPhoneNumber\Phone_number;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Repository\DataRepository;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces\ProjectDbInterface;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces\ProjectInterface;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Database\Interfaces\SmsDatabaseInterface;

/**
 * Class SmsDatabase
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Database
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class SmsDatabase implements ProjectInterface, ProjectDbInterface, SmsDatabaseInterface
{
    /** @var object \nguyenanhung\MyDebug\Benchmark */
    private $benchmark;
    /** @var object \nguyenanhung\MyDebug\Debug */
    private $debug;
    /** @var object \nguyenanhung\MyCache\Cache */
    private $cache;
    /** @var object \nguyenanhung\VnTelcoPhoneNumber\Phone_number */
    private $phoneNumber;
    /** @var object \nguyenanhung\MantisBT\MantisConnector */
    private $mantis;
    /** @var null|array */
    private $sdkConfig;
    /** @var array|null */
    private $options;

    /**
     * SmsDatabase constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (self::USE_BENCHMARK === TRUE) {
            $this->benchmark = new Benchmark();
            $this->benchmark->mark('code_start');
        }
        $this->debug       = new Debug();
        $this->phoneNumber = new Phone_number();
        $this->cache       = new Cache();
        if (isset($options['debugStatus']) && $options['debugStatus'] === TRUE) {
            $this->debug->setDebugStatus(TRUE);
            $this->phoneNumber->setDebugStatus(TRUE);
            $this->cache->setDebugStatus(TRUE);
            if (isset($options['debugLevel']) && !empty($options['debugLevel'])) {
                $this->debug->setGlobalLoggerLevel($options['debugLevel']);
                $this->phoneNumber->setDebugLevel($options['debugLevel']);
                $this->cache->setDebugLevel($options['debugLevel']);
            }
            if (isset($options['loggerPath']) && !empty($options['loggerPath'])) {
                $this->debug->setLoggerPath($options['loggerPath']);
                $this->phoneNumber->setLoggerPath($options['loggerPath']);
                $this->cache->setDebugLoggerPath($options['loggerPath']);
            }
            $this->debug->setLoggerSubPath(__CLASS__);
            $this->debug->setLoggerFilename('Log-' . date('Y-m-d') . '.log');
            $this->phoneNumber->__construct();
        }
        if (isset($options['cachePath'])) {
            $this->cache->setCachePath($options['cachePath']);
        }
        if (isset($options['cacheTtl'])) {
            $this->cache->setCacheTtl($options['cacheTtl']);
        }
        if (isset($options['cacheDriver'])) {
            $this->cache->setCacheDriver($options['cacheDriver']);
        }
        if (isset($options['cacheFileDefaultChmod'])) {
            $this->cache->setCacheDefaultChmod($options['cacheFileDefaultChmod']);
        }
        if (isset($options['cacheSecurityKey'])) {
            $this->cache->setCacheSecurityKey($options['cacheSecurityKey']);
        }
        $this->cache->__construct();
        $this->options = $options;
        $this->mantis  = new MantisConnector();
        $this->mantis->setMonitorUrl($options['monitorUrl']);
        $this->mantis->setMonitorUser($options['monitorUser']);
        $this->mantis->setMonitorPassword($options['monitorPassword']);
        $this->mantis->setProjectId($options['monitorProjectId']);
        $this->mantis->setUsername($options['monitorUsername']);
        $this->mantis->__construct();
        $this->debug->debug(__FUNCTION__, '/-------------------------> Begin Logger - SMS Database - Version: ' . self::VERSION . ' - Last Modified: ' . self::LAST_MODIFIED . ' <-------------------------\\');
    }

    /**
     * SmsDatabase destructor.
     */
    public function __destruct()
    {
        if (self::USE_BENCHMARK === TRUE) {
            $this->benchmark->mark('code_end');
            $this->debug->debug(__FUNCTION__, 'Elapsed Time: ===> ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
            $this->debug->debug(__FUNCTION__, 'Memory Usage: ===> ' . $this->benchmark->memory_usage());
        }
        $this->debug->debug(__FUNCTION__, '/-------------------------> End Logger - SMS Database - Version: ' . self::VERSION . ' - Last Modified: ' . self::LAST_MODIFIED . ' <-------------------------\\');
    }

    /**
     * Function getVersion
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 10:18
     *
     * @return mixed|string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /******************************* CONFIG *******************************/
    /**
     * Function setSdkConfig
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 10:24
     *
     * @param array $sdkConfig
     *
     * @return $this
     */
    public function setSdkConfig($sdkConfig = [])
    {
        $this->sdkConfig = $sdkConfig;
        $this->debug->debug(__FUNCTION__, 'SDK Config => ' . json_encode($this->sdkConfig));

        return $this;
    }

    /**
     * Function getSdkConfig
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 10:24
     *
     * @return mixed
     */
    public function getSdkConfig()
    {
        return $this->sdkConfig;
    }

    /**
     * Function getConfigData
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 22:22
     *
     * @return array|mixed
     */
    public function getConfigData()
    {
        $configData = DataRepository::getData('config_database');
        $this->debug->debug(__FUNCTION__, 'Config Database => ' . json_encode($configData));

        return $configData;
    }

    /******************************* DB METHOD *******************************/
    /**
     * Function connectDatabase
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 22:23
     *
     * @param array $database
     *
     * @return \nguyenanhung\MyDatabase\Model\BaseModel
     */
    public function connectDatabase($database = [])
    {
        $model = new BaseModel();
        if (isset($this->options['debugStatus']) && $this->options['debugStatus'] === TRUE) {
            $model->debugStatus = $this->options['debugStatus'];
            if (isset($this->options['debugLevel']) && !empty($this->options['debugLevel'])) {
                $model->debugLevel = $this->options['debugLevel'];
            }
            if (isset($this->options['loggerPath']) && !empty($this->options['loggerPath'])) {
                $model->debugLoggerPath = $this->options['loggerPath'];
            }
            $model->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
            $model->__construct();
        }
        $model->setDatabase($database);

        return $model;
    }

    /******************************* DB SERVICE *******************************/
    /**
     * Function getDataShortCode
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 22:40
     *
     * @param string $whereValue
     * @param string $whereField
     *
     * @return array|bool|\Illuminate\Support\Collection|mixed|null|string
     */
    public function getDataShortCode($whereValue = '', $whereField = 'shortcode')
    {
        $inputParams = [
            'whereValue' => $whereValue,
            'whereField' => $whereField,
        ];
        $this->debug->debug(__FUNCTION__, 'Input Params => ' . json_encode($inputParams));
        $cacheKey = self::PROJECT_CACHE_KEY . self::CLASS_CACHE_KEY . __FUNCTION__ . hash('md5', json_encode($whereValue) . json_encode($whereField));
        if ($this->cache->has($cacheKey)) {
            $result = $this->cache->get($cacheKey);
        } else {
            $database = $this->connectDatabase($this->sdkConfig[self::_DATABASE_CONFIG_KEY_]);
            $result   = $database->setTable(self::TABLE_SHORT_CODE)->getInfo($whereValue, $whereField);
            $this->cache->save($cacheKey, $result);
        }
        $this->debug->debug(__FUNCTION__, 'Result Data => ' . json_encode($result));

        return $result;
    }
}