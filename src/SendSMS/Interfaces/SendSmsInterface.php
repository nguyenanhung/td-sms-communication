<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 23:08
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS\Interfaces;

/**
 * Interface SendSmsInterface
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface SendSmsInterface
{
    /******************************* CONFIG *******************************/
    /**
     * Function setResponseIsObject
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:24
     *
     * @return $this
     */
    public function setResponseIsObject();

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
    public function setSdkConfig($sdkConfig = []);

    /**
     * Function getSdkConfig
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 10:24
     *
     * @return mixed
     */
    public function getSdkConfig();

    /**
     * Function getConfigData
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 22:22
     *
     * @return array|mixed
     */
    public function getConfigData();

    /******************************* GET RESULT *******************************/
    /**
     * Function getSendSmsResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:56
     *
     * @return array|null|object
     */
    public function getSendSmsResult();

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
    public function sendSmsToWebService($phone_number = '', $msg = '', $mo = '', $note = '', $method = NULL);

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
    public function sendSmsToSmppVersion2($phone_number = '', $msg = '', $callbackParams = '');

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
    public function sendSmsToSmppVersion1($phone_number = '', $msg = '');

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
    public function sendSmsToGatewayWithoutMo($phone_number = '', $msg = '', $short_code = '');

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
    public function sendSmsToGatewayWithMo($moId = '', $phone_number = '', $msg = '', $msgIndex = 1, $totalMt = 1);

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
    public function sendSmsToVasCloudGatewayVina($phone_number = '', $msg = '', $mo = '', $send_method = '');

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
    public function sendToSmsViettelWithMPSGateway($phone_number = '', $msg = '', $alias = '', $params = 'TEXT');

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
    public function sendToSmsViettelWithWebService($phone_number = '', $msg = '', $alias = '', $params = 'TEXT');
}
