<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 22:51
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Services;

use nguyenanhung\MantisBT\MantisConnector;
use nguyenanhung\MyCache\Cache;
use nguyenanhung\MyDebug\Benchmark;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyRequests\MyRequests;
use nguyenanhung\MyRequests\SoapRequest;
use nguyenanhung\VnTelcoPhoneNumber\Phone_number;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Repository\DataRepository;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces\ProjectDbInterface;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces\ProjectInterface;

/**
 * Class SendSms
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Services
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class SendSms implements ProjectInterface, ProjectDbInterface
{
    /** @var object \nguyenanhung\MyDebug\Benchmark */
    private $benchmark;
    /** @var object \nguyenanhung\MyDebug\Debug */
    private $debug;
    /** @var object \nguyenanhung\MyCache\Cache */
    private $cache;
    /** @var object \nguyenanhung\VnTelcoPhoneNumber\Phone_number */
    private $phoneNumber;
    /** @var object \nguyenanhung\MyRequests\MyRequests */
    private $requests;
    /** @var object \nguyenanhung\MyRequests\SoapRequest */
    private $soapRequests;
    /** @var object \nguyenanhung\MantisBT\MantisConnector */
    private $mantis;
    /** @var null|array */
    private $sdkConfig;
    /** @var array|null */
    private $options;

    /**
     * SendSms constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (self::USE_BENCHMARK === TRUE) {
            $this->benchmark = new Benchmark();
            $this->benchmark->mark('code_start');
        }
        $this->debug        = new Debug();
        $this->phoneNumber  = new Phone_number();
        $this->cache        = new Cache();
        $this->requests     = new MyRequests();
        $this->soapRequests = new SoapRequest();
        if (isset($options['debugStatus']) && $options['debugStatus'] === TRUE) {
            $this->debug->setDebugStatus(TRUE);
            $this->phoneNumber->setDebugStatus(TRUE);
            $this->cache->setDebugStatus(TRUE);
            $this->requests->debugStatus     = TRUE;
            $this->soapRequests->debugStatus = TRUE;
            if (isset($options['debugLevel']) && !empty($options['debugLevel'])) {
                $this->debug->setGlobalLoggerLevel($options['debugLevel']);
                $this->phoneNumber->setDebugLevel($options['debugLevel']);
                $this->cache->setDebugLevel($options['debugLevel']);
                $this->requests->debugLevel     = $options['debugLevel'];
                $this->soapRequests->debugLevel = $options['debugLevel'];
            }
            if (isset($options['loggerPath']) && !empty($options['loggerPath'])) {
                $this->debug->setLoggerPath($options['loggerPath']);
                $this->phoneNumber->setLoggerPath($options['loggerPath']);
                $this->cache->setDebugLoggerPath($options['loggerPath']);
                $this->requests->debugLoggerPath     = $options['loggerPath'];
                $this->soapRequests->debugLoggerPath = $options['loggerPath'];
            }
            $this->debug->setLoggerSubPath(__CLASS__);
            $this->debug->setLoggerFilename('Log-' . date('Y-m-d') . '.log');
            $this->requests->debugLoggerFilename     = 'Log-' . date('Y-m-d') . '.log';
            $this->soapRequests->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
            $this->requests->__construct();
            $this->soapRequests->__construct();
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
        $this->debug->debug(__FUNCTION__, '/-------------------------> Begin Logger - Send SMS - Version: ' . self::VERSION . ' - Last Modified: ' . self::LAST_MODIFIED . ' <-------------------------\\');
    }

    /**
     * SendSms destructor.
     */
    public function __destruct()
    {
        if (self::USE_BENCHMARK === TRUE) {
            $this->benchmark->mark('code_end');
            $this->debug->debug(__FUNCTION__, 'Elapsed Time: ===> ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
            $this->debug->debug(__FUNCTION__, 'Memory Usage: ===> ' . $this->benchmark->memory_usage());
        }
        $this->debug->debug(__FUNCTION__, '/-------------------------> End Logger - Send SMS - Version: ' . self::VERSION . ' - Last Modified: ' . self::LAST_MODIFIED . ' <-------------------------\\');
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

    /******************************* SEND SMS *******************************/
}