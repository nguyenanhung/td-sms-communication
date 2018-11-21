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
use nguyenanhung\MyRequests\MyRequests;
use nguyenanhung\MyRequests\SoapRequest;
use nguyenanhung\VnTelcoPhoneNumber\Phone_number;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Helper\Utils;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Repository\DataRepository;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces\ProjectInterface;
use nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS\Interfaces\SendSmsInterface;

/**
 * Class SendSms
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS
 *
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class SendSms implements ProjectInterface, SendSmsInterface
{
    /** @var object \nguyenanhung\MyDebug\Benchmark */
    private $benchmark;
    /** @var object \nguyenanhung\MyDebug\Debug */
    private $debug;
    /** @var object \nguyenanhung\VnTelcoPhoneNumber\Phone_number */
    private $phoneNumber;
    /** @var object \nguyenanhung\MyRequests\MyRequests */
    private $requests;
    /** @var object \nguyenanhung\MyRequests\SoapRequest */
    private $soapRequests;
    /** @var null|array */
    private $sdkConfig;
    /** @var array|null */
    private $options;
    /** @var bool */
    private $responseIsObject;
    /** @var null|array|object */
    private $sendSmsResult;

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
        $this->requests     = new MyRequests();
        $this->soapRequests = new SoapRequest();
        if (isset($options['debugStatus']) && $options['debugStatus'] === TRUE) {
            $this->debug->setDebugStatus(TRUE);
            $this->phoneNumber->setDebugStatus(TRUE);
            $this->requests->debugStatus     = TRUE;
            $this->soapRequests->debugStatus = TRUE;
            if (isset($options['debugLevel']) && !empty($options['debugLevel'])) {
                $this->debug->setGlobalLoggerLevel($options['debugLevel']);
                $this->phoneNumber->setDebugLevel($options['debugLevel']);
                $this->requests->debugLevel     = $options['debugLevel'];
                $this->soapRequests->debugLevel = $options['debugLevel'];
            }
            if (isset($options['loggerPath']) && !empty($options['loggerPath'])) {
                $this->debug->setLoggerPath($options['loggerPath']);
                $this->phoneNumber->setLoggerPath($options['loggerPath']);
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
        $this->options = $options;
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
     * Function setResponseIsObject
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:24
     *
     * @return $this
     */
    public function setResponseIsObject()
    {
        $this->responseIsObject = TRUE;

        return $this;
    }

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
        $configData = DataRepository::getData('sdk_config_send_sms');
        $this->debug->debug(__FUNCTION__, 'Config Database => ' . json_encode($configData));

        return $configData;
    }

    /******************************* GET RESULT *******************************/
    /**
     * Function getSendSmsResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:56
     *
     * @return array|null|object
     */
    public function getSendSmsResult()
    {
        return $this->sendSmsResult;
    }

    /******************************* SEND SMS *******************************/
    /**
     * Function sendSmsToWebService
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:58
     *
     * @param string      $phone_number
     * @param string      $msg
     * @param string      $mo
     * @param string      $note
     * @param null|string $method
     *
     * @return $this
     */
    public function sendSmsToWebService($phone_number = '', $msg = '', $mo = '', $note = '', $method = NULL)
    {
        $inputParams = [
            'phone_number' => $phone_number,
            'msg'          => $msg,
            'mo'           => $mo,
            'note'         => $note,
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $msg          = trim($msg);
        $note         = trim($note);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['url'];
        if (empty($method)) {
            $method = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['method'];
        }
        $method   = strtoupper($method);
        $validStr = $phone_number . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['prefix'] . $msg . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['prefix'] . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['private_token'];
        $params   = [
            'msisdn'    => $phone_number,
            'mo'        => $mo,
            'mt'        => $msg,
            'note'      => $note . self::NOTE_PREFIX . $phone_number . self::NOTE_PREFIX . date('Y-m-d') . self::NOTE_PREFIX . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['shortcode'],
            'signature' => md5($validStr)
        ];
        $this->debug->debug(__FUNCTION__, 'SendSms To Webservice');
        $this->debug->debug(__FUNCTION__, 'SendSms Method: ' . json_encode($method));
        $this->debug->debug(__FUNCTION__, 'SendSms Url: ' . json_encode($url));
        $this->debug->debug(__FUNCTION__, 'SendSms Params: ' . json_encode($params));
        $this->debug->debug(__FUNCTION__, 'SendSms String to Valid: ' . json_encode($validStr));
        $this->debug->debug(__FUNCTION__, 'SendSms Token: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['private_token']));
        $this->debug->debug(__FUNCTION__, 'SendSms Prefix: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_WEB_SERVICE]['prefix']));
        if ($method == 'JSON') {
            $requestSms = $this->requests->jsonRequest($url, $params);
        } elseif ($method == 'XML') {
            $requestSms = $this->requests->xmlRequest($url, $params);
        } elseif ($method == 'POST') {
            $requestSms = $this->requests->sendRequest($url, $params, 'POST');
        } else {
            $requestSms = $this->requests->sendRequest($url, $params);
        }
        $this->debug->debug(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        $responseSms = json_decode(trim($requestSms));
        if ($responseSms === NULL || empty($responseSms)) {
            $result = [
                'code'        => self::API_ERROR_CODE,
                'description' => 'Error',
                'data'        => [
                    'msg'           => 'Không gửi được MT. Response từ API về không hợp lệ',
                    'requestResult' => $requestSms
                ]
            ];
        } else {
            if (isset($responseSms->ec) && $responseSms->ec == self::SEND_SMS_SUCCESS) {
                $result = [
                    'code'        => self::EXIT_SUCCESS,
                    'description' => 'Success'
                ];
            } else {
                $result = [
                    'code'        => self::EXIT_ERROR,
                    'description' => 'Failed'
                ];
            }
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendSmsToSmppVersion2
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:38
     *
     * @param string $phone_number
     * @param string $msg
     * @param string $callbackParams
     *
     * @return $this
     */
    public function sendSmsToSmppVersion2($phone_number = '', $msg = '', $callbackParams = '')
    {
        $inputParams = [
            'phone_number'   => $phone_number,
            'msg'            => $msg,
            'callbackParams' => $callbackParams
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $msg          = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_2]['url'];
        $method       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_2]['method'];
        $callBack     = (!empty($this->sdkConfig['callbackUrl'])) ? trim($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_2]['callbackUrl']) . trim(base64_encode($callbackParams), '=') : NULL;
        $params       = [
            'service'     => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_2]['shortcode'],
            'msisdn'      => $phone_number,
            'msg'         => $msg,
            'callbackUrl' => $callBack
        ];
        $this->debug->debug(__FUNCTION__, 'SendSms To SMPP Version 2');
        $this->debug->debug(__FUNCTION__, 'SendSms Method: ' . json_encode($method));
        $this->debug->debug(__FUNCTION__, 'SendSms Url: ' . json_encode($url));
        $this->debug->debug(__FUNCTION__, 'SendSms Params: ' . json_encode($params));
        $requestSms = $this->requests->sendRequest($url, $params, $method);
        $this->debug->debug(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        $responseSms = json_decode(trim($requestSms));
        if ($responseSms === NULL || empty($responseSms)) {
            $result = [
                'code'        => self::API_ERROR_CODE,
                'description' => 'Error',
                'data'        => [
                    'msg'           => 'Không gửi được MT. Response từ API về không hợp lệ',
                    'requestResult' => $requestSms
                ]
            ];
        } else {
            if (isset($responseSms->ec) && $responseSms->ec == self::SEND_SMS_SUCCESS) {
                $result = [
                    'code'        => self::EXIT_SUCCESS,
                    'description' => 'Success',
                    'data'        => [
                        'requestResult' => $requestSms
                    ]
                ];
            } else {
                $result = [
                    'code'        => self::EXIT_ERROR,
                    'description' => 'Failed',
                    'data'        => [
                        'requestResult' => $requestSms
                    ]
                ];
            }
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendSmsToSmppVersion1
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:37
     *
     * @param string $phone_number
     * @param string $msg
     *
     * @return $this
     */
    public function sendSmsToSmppVersion1($phone_number = '', $msg = '')
    {
        $inputParams = [
            'phone_number' => $phone_number,
            'msg'          => $msg
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $msg          = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_1]['url'];
        $method       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_1]['method'];
        $params       = [
            'username' => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_1]['username'],
            'password' => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_1]['password'],
            'to'       => $phone_number,
            'text'     => $msg
        ];
        $this->debug->debug(__FUNCTION__, 'SendSms To SMPP Version 1');
        $this->debug->debug(__FUNCTION__, 'SendSms Method: ' . json_encode($method));
        $this->debug->debug(__FUNCTION__, 'SendSms Url: ' . json_encode($url));
        $this->debug->debug(__FUNCTION__, 'SendSms Params: ' . json_encode($params));
        $this->debug->debug(__FUNCTION__, 'SendSms Token: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_1]['private_token']));
        $this->debug->debug(__FUNCTION__, 'SendSms Prefix: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMPP_VERSION_1]['prefix']));
        $responseSms = $this->requests->sendRequest($url, $params, $method);
        $this->debug->debug(__FUNCTION__, 'SendSms Result: ' . json_encode($responseSms));
        if ($responseSms === NULL || empty($responseSms)) {
            $result = [
                'code'        => self::API_ERROR_CODE,
                'description' => 'Error',
                'data'        => [
                    'msg'           => 'Không gửi được MT. Response từ API về không hợp lệ',
                    'requestResult' => $responseSms
                ]
            ];
        } else {
            if ($responseSms == self::SMPP_VER_1_SUCCESS) {
                $result = [
                    'code'        => self::EXIT_SUCCESS,
                    'description' => 'Success',
                    'data'        => [
                        'requestResult' => $responseSms
                    ]
                ];
            } else {
                $result = [
                    'code'        => self::EXIT_ERROR,
                    'description' => 'Failed',
                    'data'        => [
                        'requestResult' => $responseSms
                    ]
                ];
            }
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendSmsToGatewayWithoutMo
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:37
     *
     * @param string $phone_number
     * @param string $msg
     * @param string $short_code
     *
     * @return $this
     */
    public function sendSmsToGatewayWithoutMo($phone_number = '', $msg = '', $short_code = '')
    {
        $inputParams = [
            'phone_number' => $phone_number,
            'msg'          => $msg,
            'short_code'   => $short_code
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        // Kết nối theo mẫu tài liệu kết nối gửi MT chủ động
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $msg          = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['url'];
        $method       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['method'];
        $validStr     = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['pnId'] . $phone_number . $msg . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['private_token'];
        $params       = [
            // Tham số API
            'api'       => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['api'],
            // Số điện thoại cần gửi
            'phone'     => $phone_number,
            // Nội dung tin nhắn
            'message'   => $msg,
            // Đầu số gửi tin nhắn
            'service'   => $short_code,
            // Tham số định danh đối tác
            'pnid'      => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['pnId'],
            // Chữ ký xác thực
            'signature' => md5($validStr)
        ];
        $this->debug->debug(__FUNCTION__, 'SendSms To SMS Gateway Without MO');
        $this->debug->debug(__FUNCTION__, 'SendSms Method: ' . json_encode($method));
        $this->debug->debug(__FUNCTION__, 'SendSms Url: ' . json_encode($url));
        $this->debug->debug(__FUNCTION__, 'SendSms Params: ' . json_encode($params));
        $this->debug->debug(__FUNCTION__, 'SendSms String to Valid: ' . json_encode($validStr));
        $this->debug->debug(__FUNCTION__, 'SendSms Token: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['private_token']));
        $this->debug->debug(__FUNCTION__, 'SendSms Prefix: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['prefix']));
        $requestSms = $this->requests->sendRequest($url, $params, $method);
        $this->debug->debug(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        if (array_key_exists($requestSms, $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['responseMap'])) {
            $result = [
                'status' => $requestSms,
                'desc'   => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO]['responseMap'][$requestSms]
            ];
        } else {
            $result = [
                'status' => self::EXIT_ERROR,
                'desc'   => 'Gửi tin thất bại'
            ];
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendSmsToGatewayWithMo
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:52
     *
     * @param string $moId
     * @param string $phone_number
     * @param string $msg
     * @param int    $msgIndex
     * @param int    $totalMt
     *
     * @return $this
     */
    public function sendSmsToGatewayWithMo($moId = '', $phone_number = '', $msg = '', $msgIndex = 1, $totalMt = 1)
    {
        $inputParams = [
            'moId'         => $moId,
            'phone_number' => $phone_number,
            'msg'          => $msg,
            'msgIndex'     => $msgIndex,
            'totalMt'      => $totalMt
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        // Kết nối theo mẫu tài liệu kết nối gửi MT chủ động
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $telco_id     = $this->phoneNumber->detect_carrier($phone_number, TRUE);
        $msg          = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['url'];
        $method       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['method'];
        $validStr     = $moId . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['prefix'] . $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['private_token'];
        $params       = [
            // Tham số API
            'pnid'     => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['pnId'],
            'npid'     => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['npId'],
            'moid'     => $moId,
            'receiver' => $phone_number,
            'type'     => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['type'],
            'message'  => $msg,
            'cpid'     => $telco_id,
            'msgindex' => (empty($msgIndex)) ? $msgIndex : $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['msgindex'],
            'totalMT'  => (empty($totalMt)) ? $totalMt : $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['totalMT'],
            'signal'   => md5($validStr)
        ];
        $this->debug->debug(__FUNCTION__, 'SendSms To SMS Gateway With MoId');
        $this->debug->debug(__FUNCTION__, 'SendSms Method: ' . json_encode($method));
        $this->debug->debug(__FUNCTION__, 'SendSms Url: ' . json_encode($url));
        $this->debug->debug(__FUNCTION__, 'SendSms Params: ' . json_encode($params));
        $this->debug->debug(__FUNCTION__, 'SendSms String to Valid: ' . json_encode($validStr));
        $this->debug->debug(__FUNCTION__, 'SendSms Token: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['private_token']));
        $this->debug->debug(__FUNCTION__, 'SendSms Prefix: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['prefix']));
        $requestSms = $this->requests->sendRequest($url, $params, $method);
        $this->debug->debug(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        if (array_key_exists($requestSms, $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['responseMap'])) {
            $result = [
                'status' => $requestSms,
                'desc'   => $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_WITH_MO]['responseMap'][$requestSms]
            ];
        } else {
            $result = [
                'status' => self::EXIT_ERROR,
                'desc'   => 'Gửi tin thất bại'
            ];
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendSmsToVasCloudGatewayVina
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:37
     *
     * @param string $phone_number
     * @param string $msg
     * @param string $mo
     * @param string $send_method
     *
     * @return $this
     */
    public function sendSmsToVasCloudGatewayVina($phone_number = '', $msg = '', $mo = '', $send_method = '')
    {
        $inputParams = [
            'phone_number' => $phone_number,
            'msg'          => $msg,
            'mo'           => $mo,
            'send_method'  => $send_method
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $mo           = trim($mo);
        $msg          = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['url'];
        $method       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['method'];
        // Send SMS to VasCloud
        $transaction_id     = ceil(microtime(TRUE) * 1000);
        $default_moid       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['default_moid'];
        $source_address     = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['shortcode'];
        $sms_brandname      = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['brandname'];
        $sms_service_code   = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['service_code'];
        $sms_content_type   = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['content_type'];
        $sms_user_name      = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['username_cp'];
        $authenticate       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['authenticate'];
        $acount_send_sms    = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['account'];
        $sms_cp_code        = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['cp_code'];
        $sms_cp_charge      = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['cp_charge'];
        $price              = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['default_price'];
        $input_package_code = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['default_package'];
        $sms_authenticate   = md5(md5($transaction_id . $sms_user_name) . md5($acount_send_sms . $phone_number) . $authenticate);
        // Data XML gui len SMSGW Vascloud
        $content_xml = "<MODULE>SMSGW</MODULE><MESSAGE_TYPE>REQUEST</MESSAGE_TYPE><COMMAND><transaction_id>$transaction_id</transaction_id>" . "<mo_id>" . $default_moid . "</mo_id><destination_address>" . $phone_number . "</destination_address><source_address>" . $source_address . "</source_address>" . "<brandname>" . $sms_brandname . "</brandname><content_type>" . $sms_content_type . "</content_type><user_name>" . $sms_user_name . "</user_name>" . "<authenticate>" . $sms_authenticate . "</authenticate><info>" . $msg . "</info><command_code>" . $mo . "</command_code>" . "<cp_code>" . $sms_cp_code . "</cp_code><cp_charge>" . $sms_cp_charge . "</cp_charge><service_code>" . $sms_service_code . "</service_code>" . "<package_code>$input_package_code</package_code><package_price>$price</package_price></COMMAND>";
        $data_xml    = '<?xml version="1.0" encoding="utf-8"?><ACCESSGW xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $content_xml . '</ACCESSGW>';
        $this->debug->info(__FUNCTION__, 'Send Request ' . $method . ' to URL ' . $url);
        $this->debug->info('Send Request Data ' . $data_xml);
        $requestSms = (($send_method !== NULL) && ($send_method == 'Msg_Log')) ? $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['responseIsSuccess'] : $this->requests->xmlRequest($url, $data_xml, $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['timeout']);
        $this->debug->info(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        if (empty($requestSms)) {
            $result = [
                'status' => self::EXIT_ERROR,
                'msg'    => 'Parse Request is Error'
            ];
        } else {
            $errorId   = $this->requests->xmlGetValue($requestSms, "<error_id>", "</error_id>");
            $errorDesc = $this->requests->xmlGetValue($requestSms, "<error_desc>", "</error_desc>");
            if ($errorId == self::SEND_SMS_SUCCESS) {
                $result = [
                    'status' => self::EXIT_SUCCESS,
                    'msg'    => 'Gửi tin nhắn thành công',
                    'data'   => [
                        'error_id'   => $errorId,
                        'error_desc' => $errorDesc,
                    ]
                ];
            } else {
                $result = [
                    'status' => self::EXIT_ERROR,
                    'msg'    => 'Gửi tin nhắn thất bại',
                    'data'   => [
                        'error_id'   => $errorId,
                        'error_desc' => $errorDesc,
                    ]
                ];
            }
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendToSmsViettelWithMPSGateway
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:55
     *
     * @param string $phone_number
     * @param string $msg
     * @param string $alias
     * @param string $params
     *
     * @return $this
     */
    public function sendToSmsViettelWithMPSGateway($phone_number = '', $msg = '', $alias = '', $params = 'TEXT')
    {
        $inputParams = [
            'phone_number' => $phone_number,
            'msg'          => $msg,
            'alias'        => $alias,
            'params'       => $params
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $content      = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['api_link'];
        $username     = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['username'];
        $password     = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['password'];
        $shortcode    = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['shortcode'];
        $timeout      = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['timeout'];
        // Request Data với XML thuần
        $data_xml = '<?xml version="1.0"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://smsws/xsd"><soapenv:Header/><soapenv:Body><xsd:smsRequest><xsd:username>' . $username . '</xsd:username><xsd:password>' . $password . '</xsd:password><xsd:msisdn>' . $phone_number . '</xsd:msisdn><xsd:content>' . $content . '</xsd:content><xsd:shortcode>' . $shortcode . '</xsd:shortcode><xsd:alias>' . $alias . '</xsd:alias><xsd:params>' . $params . '</xsd:params></xsd:smsRequest></soapenv:Body></soapenv:Envelope>';
        $this->debug->info(__FUNCTION__, 'Send Request to URL ' . $url);
        $this->debug->info('Send Request Data ' . $data_xml);
        $requestSms = $this->requests->xmlRequest($url, $data_xml, $timeout); // Gửi Request SMS
        $this->debug->info(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        $returnResult = $this->requests->xmlGetValue($requestSms, "<return>", "</return>"); // Display the result
        if ($returnResult == self::SEND_SMS_SUCCESS) {
            $result = [
                'status' => self::EXIT_SUCCESS,
                'msg'    => 'Gửi tin nhắn thành công',
                'data'   => [
                    'returnResult'  => $returnResult,
                    'requestResult' => $requestSms
                ]
            ];
        } else {
            if (array_key_exists($returnResult, $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['responseMap'])) {
                $result = [
                    'status' => self::EXIT_ERROR,
                    'msg'    => 'Gửi tin nhắn thất bại',
                    'data'   => [
                        'returnResult'     => $returnResult,
                        'returnResultDesc' => Utils::strToEn($this->sdkConfig['responseMap'][$returnResult]),
                        'requestResult'    => $requestSms
                    ]
                ];
            } else {
                $result = [
                    'status' => self::EXIT_CONFIG,
                    'msg'    => 'Lỗi hệ thống',
                    'data'   => [
                        'returnResult'  => $returnResult,
                        'requestResult' => $requestSms
                    ]
                ];
            }
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }

    /**
     * Function sendToSmsViettelWithWebService
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:55
     *
     * @param string $phone_number
     * @param string $msg
     * @param string $alias
     * @param string $params
     *
     * @return $this
     */
    public function sendToSmsViettelWithWebService($phone_number = '', $msg = '', $alias = '', $params = 'TEXT')
    {
        $inputParams = [
            'phone_number' => $phone_number,
            'msg'          => $msg,
            'alias'        => $alias,
            'params'       => $params
        ];
        $this->debug->info(__FUNCTION__, 'Input Params: ' . json_encode($inputParams));
        $phone_number = $this->phoneNumber->vn_convert_phone_number($phone_number, 'new');
        $content      = trim($msg);
        $url          = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['api_link'];
        $token        = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['private_token'];
        $prefix       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['prefix'];
        $method       = $this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['method'];
        $validStr     = $phone_number . $prefix . $content . $prefix . $params . $prefix . $token;
        // Request Data với XML thuần
        $params = [
            'msisdn'    => $phone_number,
            'content'   => $content,
            'alias'     => $alias,
            'type'      => $params,
            'signature' => md5($validStr)
        ];
        $this->debug->debug(__FUNCTION__, 'SendSms To Webservice');
        $this->debug->debug(__FUNCTION__, 'SendSms Method: ' . json_encode($method));
        $this->debug->debug(__FUNCTION__, 'SendSms Url: ' . json_encode($url));
        $this->debug->debug(__FUNCTION__, 'SendSms Params: ' . json_encode($params));
        $this->debug->debug(__FUNCTION__, 'SendSms String to Valid: ' . json_encode($validStr));
        $this->debug->debug(__FUNCTION__, 'SendSms Token: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['private_token']));
        $this->debug->debug(__FUNCTION__, 'SendSms Prefix: ' . json_encode($this->sdkConfig[self::_SMS_GATEWAY_][self::CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD]['prefix']));
        $requestSms = $this->requests->sendRequest($url, $params, $method);
        $this->debug->debug(__FUNCTION__, 'SendSms Result: ' . json_encode($requestSms));
        $responseSms = json_decode(trim($requestSms));
        if ($responseSms === NULL || empty($responseSms)) {
            $result = [
                'code'        => self::API_ERROR_CODE,
                'description' => 'Error',
                'data'        => [
                    'msg'           => 'Không gửi được MT. Response từ API về không hợp lệ',
                    'requestResult' => $requestSms
                ]
            ];
        } else {
            if (isset($responseSms->Result) && $responseSms->Result == self::SEND_SMS_SUCCESS) {
                $result = [
                    'code'        => self::EXIT_SUCCESS,
                    'description' => 'Success',
                    'data'        => [
                        'requestResult' => $responseSms
                    ]
                ];
            } else {
                $result = [
                    'code'        => self::EXIT_ERROR,
                    'description' => 'Failed',
                    'data'        => [
                        'requestResult' => $responseSms
                    ]
                ];
            }
        }
        if ($this->responseIsObject) {
            $result = Utils::arrayToObject($result);
        }
        $this->sendSmsResult = $result;
        $this->debug->info(__FUNCTION__, 'Send SMS Result: ' . json_encode($this->sendSmsResult));

        return $this;
    }
}
