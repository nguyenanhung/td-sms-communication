<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 23:08
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS;

use nguyenanhung\MyDebug\Benchmark;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\VnTelcoPhoneNumber\Phone_number;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces\ProjectInterface;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Repository\DataRepository;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS\Interfaces\SendSmsCallbackInterface;

/**
 * Class SendSmsCallback
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class SendSmsCallback implements ProjectInterface, SendSmsCallbackInterface
{
    /** @var object \nguyenanhung\MyDebug\Benchmark */
    private $benchmark;
    /** @var object \nguyenanhung\MyDebug\Debug */
    private $debug;
    /** @var object \nguyenanhung\VnTelcoPhoneNumber\Phone_number */
    private $phoneNumber;
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
        $this->debug       = new Debug();
        $this->phoneNumber = new Phone_number();
        if (isset($options['debugStatus']) && $options['debugStatus'] === TRUE) {
            $this->debug->setDebugStatus(TRUE);
            $this->phoneNumber->setDebugStatus(TRUE);
            if (isset($options['debugLevel']) && !empty($options['debugLevel'])) {
                $this->debug->setGlobalLoggerLevel($options['debugLevel']);
                $this->phoneNumber->setDebugLevel($options['debugLevel']);
            }
            if (isset($options['loggerPath']) && !empty($options['loggerPath'])) {
                $this->debug->setLoggerPath($options['loggerPath']);
                $this->phoneNumber->setLoggerPath($options['loggerPath']);
            }
            $this->debug->setLoggerSubPath(__CLASS__);
            $this->debug->setLoggerFilename('Log-' . date('Y-m-d') . '.log');
            $this->phoneNumber->__construct();
        }
        $this->options = $options;
        $this->debug->debug(__FUNCTION__, '/-------------------------> Begin Logger - Send SMS Callback - Version: ' . self::VERSION . ' - Last Modified: ' . self::LAST_MODIFIED . ' <-------------------------\\');
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
        $configData = DataRepository::getData('callbackSms');
        $this->debug->debug(__FUNCTION__, 'Config Callback SMS => ' . json_encode($configData));

        return $configData;
    }

    /******************************* SERVICE *******************************/
    /**
     * Function initializeCallback
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:16
     *
     * @param string $callbackCode
     * @param string $statusCode
     *
     * @return int
     */
    public function initializeCallback($callbackCode = '', $statusCode = '')
    {
        $callback = $this->getConfigData();
        if (!empty($callbackCode)) {
            $decodeData = base64_decode(trim($callbackCode));
            $initData   = explode(self::NOTE_PREFIX, $decodeData);
            if (is_array($initData) && count($initData) >= 2) {
                /**
                 * Phân tích dữ liệu,
                 * chia thành nhiều mảng callback với những kiểu dữ liệu khác nhau
                 */
                $listId = [self::SEND_SMS_ID, self::FORWARD_SMS_ID, self::DAILY_SMS_ID, self::PUSH_SMS_ID];
                $json   = in_array($initData[0], $listId) ? json_decode(trim($initData[1])) : NULL;
                if ($json !== NULL) {
                    switch ($initData[0]) {
                        case self::SEND_SMS_ID:
                            $msgLog = sprintf($callback[self::SEND_SMS_ID], isset($json->time) ? $json->time : NULL, isset($json->shortcode) ? $json->shortcode : NULL, isset($json->msisdn) ? $json->msisdn : NULL, isset($json->msg) ? $json->msg : NULL, $statusCode);
                            break;
                        case self::FORWARD_SMS_ID:
                            $msgLog = sprintf($callback[self::FORWARD_SMS_ID], isset($json->time) ? $json->time : NULL, isset($json->shortcode) ? $json->shortcode : NULL, isset($json->msisdn) ? $json->msisdn : NULL, isset($json->msg) ? $json->msg : NULL, $statusCode);
                            break;
                        case self::DAILY_SMS_ID:
                            $msgLog = sprintf($callback[self::FORWARD_SMS_ID], isset($json->time) ? $json->time : NULL, isset($json->shortcode) ? $json->shortcode : NULL, isset($json->msisdn) ? $json->msisdn : NULL, isset($json->serviceId) ? $json->serviceId : NULL, isset($json->packageId) ? $json->packageId : NULL, isset($json->msg) ? $json->msg : NULL, $statusCode);
                            break;
                        case self::PUSH_SMS_ID:
                            $msgLog = sprintf($callback[self::FORWARD_SMS_ID], isset($json->time) ? $json->time : NULL, isset($json->shortcode) ? $json->shortcode : NULL, isset($json->msisdn) ? $json->msisdn : NULL, isset($json->serviceId) ? $json->serviceId : NULL, isset($json->optionId) ? $json->optionId : NULL, $statusCode);
                            break;
                        default:
                            $msgLog = 'Missing Callback with statusCode: ' . $statusCode;
                    }
                    $this->debug->info('callbackMsg', $msgLog);

                    return self::EXIT_SUCCESS;
                }
            }
        }

        return self::EXIT_ERROR;
    }
}
