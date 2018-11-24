<?php
/**
 * Project td-daily-sms.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/5/18
 * Time: 23:05
 */
return [
    'sendSms'    => [
        'id'      => 'sendSms',
        'pattern' => 'Time: %s | Shortcode: %s | Msisdn: %s | Msg: %s | StatusCode: %s'
    ],
    'forwardSms' => [
        'id'      => 'forwardSms',
        'pattern' => 'Time: %s | Shortcode: %s | Msisdn: %s | Msg: %s | StatusCode: %s'
    ],
    'dailySms'   => [
        'id'      => 'dailySms',
        'pattern' => 'Time: %s | Shortcode: %s | Msisdn: %s | ServiceId: %s | PackageId: %s | Msg | StatusCode: %s'
    ],
    'pushSms'    => [
        'id'      => 'pushSms',
        'pattern' => 'Time: %s | Shortcode: %s | Msisdn: %s | ServiceId: %s | OptionId: %s | StatusCode: %s'
    ]
];
