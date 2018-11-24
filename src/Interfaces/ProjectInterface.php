<?php
/**
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/4/18
 * Time: 14:55
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces;

/**
 * Interface ProjectInterface
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface ProjectInterface
{
    const VERSION                = '1.0.1';
    const LAST_MODIFIED          = '2018-11-24';
    const AUTHOR_NAME            = 'Hung Nguyen';
    const AUTHOR_EMAIL           = 'dev@nguyenanhung.com';
    const PROJECT_NAME           = 'ThuDoMultimedia Tools SMS Communication';
    const PROJECT_CACHE_KEY      = 'THU-DO-MULTIMEDIA-TOOLS-SMS-COMMUNICATION';
    const TIMEZONE               = 'Asia/Ho_Chi_Minh';
    const EXIT_SUCCESS           = 0; // no errors
    const EXIT_ERROR             = 1; // generic error
    const EXIT_CONFIG            = 3; // configuration error
    const EXIT_UNKNOWN_FILE      = 4; // file not found
    const EXIT_UNKNOWN_CLASS     = 5; // unknown class
    const EXIT_UNKNOWN_METHOD    = 6; // unknown class member
    const EXIT_USER_INPUT        = 7; // invalid user input
    const EXIT_DATABASE          = 8; // database error
    const EXIT__AUTO_MIN         = 9; // lowest automatically-assigned error code
    const EXIT__AUTO_MAX         = 125; // highest automatically-assigned error code
    const USE_BENCHMARK          = TRUE;
    const USE_DEBUG              = FALSE;
    const NOTE_PREFIX            = '|';
    const SEND_SMS_SUCCESS       = 0;
    const SMPP_VER_1_SUCCESS     = '0: Accepted for delivery';
    const API_ERROR_CODE         = 3;
    const API_CONTENT_HAS_UPDATE = 0;

    // Config SDK
    const _SMS_GATEWAY_                         = '_SMS_GATEWAY_SERVICES_';
    const CONFIG_KEY_WEB_SERVICE                = 'WebService';
    const CONFIG_KEY_SMPP_VERSION_1             = 'SMPPVersion1';
    const CONFIG_KEY_SMPP_VERSION_2             = 'SMPPVersion2';
    const CONFIG_KEY_SMS_GATEWAY                = 'SmsGateway';
    const CONFIG_KEY_SMS_GATEWAY_WITH_MO        = 'SmsGatewayWithMo';
    const CONFIG_KEY_SMS_GATEWAY_WITHOUT_MO     = 'SmsGatewayWithoutMo';
    const CONFIG_KEY_SMS_GATEWAY_VINA_VAS_CLOUD = 'SmsGatewayVinaVasCloud';
    const CONFIG_KEY_SMS_GATEWAY_VIETTEL        = 'SmsGatewayViettel';
    const CONFIG_KEY_SMS_SERVICE_VIETTEL        = 'SmsServiceViettel';

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     *
     * @return mixed|string Current Project Version, VD: 0.1.0
     */
    public function getVersion();
}
