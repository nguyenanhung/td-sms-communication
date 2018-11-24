<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 22:26
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Database\Interfaces;

/**
 * Interface SmsDatabaseInterface
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Database\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface SmsDatabaseInterface
{
    const CLASS_CACHE_KEY = '-SMS-DATABASE-';

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
    public function connectDatabase($database = []);

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
    public function getDataShortCode($whereValue = '', $whereField = 'shortcode');
}
