<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 22:12
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces;

/**
 * Interface ProjectDbInterface
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface ProjectDbInterface
{
    // CONFIG KEY
    const _DATABASE_CONFIG_KEY_     = 'DATABASE';
    const _DATABASE_LOG_CONFIG_KEY_ = 'DATABASE_LOG';

    // TABLE CONFIG
    const TABLE_SHORT_CODE = 'shortcodes';
}
