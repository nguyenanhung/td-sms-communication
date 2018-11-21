<?php
/**
 * Project td-sms-communication.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/21/18
 * Time: 23:22
 */

namespace nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Helper;

use Cocur\Slugify\Slugify;

/**
 * Class Utils
 *
 * @package   nguyenanhung\ThuDoMultimediaSMS\Tools\CommunicationSMS\Helper
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class Utils
{
    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * @param    string $uri    URL
     * @param    string $method Redirect method
     *                          'auto', 'location' or 'refresh'
     * @param    int    $code   HTTP Response status code
     *
     * @return    void
     *
     * @copyright https://www.codeigniter.com/
     */
    public static function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) OR !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                    ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }
        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, TRUE, $code);
                break;
        }
        exit;
    }

    /**
     * Function arrayToObject
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/9/18 17:01
     *
     * @param array $data
     * @param bool  $str_to_lower
     *
     * @return array|bool|\stdClass
     */
    public static function arrayToObject($data = [], $str_to_lower = FALSE)
    {
        if (!is_array($data)) {
            return $data;
        }
        $object = new \stdClass();
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $name => $value) {
                $name = trim($name);
                if ($str_to_lower === TRUE) {
                    $name = strtolower($name);
                }
                if (!empty($name)) {
                    $object->$name = self::arrayToObject($value, $str_to_lower);
                }
            }

            return $object;
        }

        return FALSE;
    }

    /**
     * Function strToEn
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/21/18 23:29
     *
     * @param string $str
     *
     * @return string
     */
    public static function strToEn($str = '')
    {
        try {
            $options = [
                'separator' => ' ',
                'lowercase' => FALSE,
            ];
            $content = new Slugify($options);

            return $content->slugify($str);
        }
        catch (\Exception $e) {
            return $str;
        }
    }
}
