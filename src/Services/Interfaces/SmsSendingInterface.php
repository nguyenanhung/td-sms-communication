<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 22:53
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Services\Interfaces;

/**
 * Interface SmsSendingInterface
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Services\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface SmsSendingInterface
{
    /******************************* CONFIG *******************************/
    /**
     * Function setResponseIsObject
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/22/18 00:22
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

    /******************************* INPUT DATA & RESULT *******************************/
    /**
     * Function setInputData
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/22/18 00:08
     *
     * @param array $inputData
     *
     * @return $this
     */
    public function setInputData($inputData = []);

    /**
     * Function getInputData
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/22/18 00:08
     *
     * @return mixed
     */
    public function getInputData();

    /**
     * Function getSmsSendingResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/22/18 00:10
     *
     * @return array|null|object
     */
    public function getSmsSendingResult();

    /******************************* SEND SMS *******************************/
    /**
     * Function smsSending
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/22/18 00:23
     *
     * @return $this
     */
    public function smsSending();
}
