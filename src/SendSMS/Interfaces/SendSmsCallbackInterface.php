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
 * Interface SendSmsCallbackInterface
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\SendSMS\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface SendSmsCallbackInterface
{
    const SEND_SMS_ID    = 'sendSms';
    const FORWARD_SMS_ID = 'forwardSms';
    const DAILY_SMS_ID   = 'dailySms';
    const PUSH_SMS_ID    = 'pushSms';

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
    public function initializeCallback($callbackCode = '', $statusCode = '');
}
