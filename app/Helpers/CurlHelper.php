<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/2
 */

namespace App\Helpers;


class CurlHelper
{
    
    /**
     * 设置过的选项
     */
    private const SKIP_OPTIONS = [
        'HEADER' => true,
        'COOKIE' => true,
        'TIMEOUT' => true,
        'HTTPHEADER' => true,
    ];
    
    /**
     * 常用的curl option参数名映射, 避免检索全局
     * @var array
     */
    private static array $curlOptionMap = [
        'HTTPHEADER' => CURLOPT_HTTPHEADER,
        'TIMEOUT' => CURLOPT_TIMEOUT,
        'TIMEOUT_MS' => CURLOPT_TIMEOUT_MS,
        'POSTFIELDS' => CURLOPT_POSTFIELDS,
        'HEADER' => CURLOPT_HEADER,
        'CONNECTTIMEOUT' => CURLOPT_CONNECTTIMEOUT,
        'CONNECTTIMEOUT_MS' => CURLOPT_CONNECTTIMEOUT_MS,
        'FOLLOWLOCATION' => CURLOPT_FOLLOWLOCATION,
        'BUFFERSIZE' => CURLOPT_BUFFERSIZE,
        'PROXY' => CURLOPT_PROXY,
        'USERAGENT' => CURLOPT_USERAGENT,
        'COOKIE' => CURLOPT_COOKIE,
    ];
    
    /**
     * @var array|null
     */
    private static ?array $lastRequestInfo;
    
    
    
    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return bool|string|null
     * @throws \Exception
     */
    public static function get($url, $data = [], array $options = [])
    {
        if (empty($data)) {
            return self::curl($url, [], $options);
        }
        !is_array($data) && $data = [];
        // 拼接参数到url中
        $url = \strpos($url, '?') ? \rtrim($url, '&') . '&' . \http_build_query($data) : \rtrim($url, '?') . '?' . \http_build_query($data);
        return self::curl($url, [], $options);
    }
    
    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return bool|string|null
     * @throws \Exception
     */
    public static function post($url, $data = [], array $options = [])
    {
        return self::curl($url, $data, $options);
    }
    
    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return bool|string|null
     * @throws \Exception
     */
    public static function curl($url, $data = [], array $options = [])
    {
        if (empty($url)) {
            return null;
        }
        
        // 自动添加HTTP
        if (!\preg_match('/^https?:\/\/(?:.*)/', $url)) {
            $url = 'http://' . $url;
        }
        
        // 处理$options
        if (\count($options)) {
            $options = \array_change_key_case($options, \CASE_UPPER);
            $tmp = [];
            foreach ($options as $key => $option) {
                $tmp[\str_replace('CURLOPT_', '', $key)] = $option;
            }
            $options = $tmp;
            unset($tmp);
        }
        
        // 初始化
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        // 设置HTTPHEADER
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::formatRequestHeaders($options['HTTPHEADER'] ?? []));
        
        // 设置超时时间 默认5秒
        $timeout = $originalTimeout = !empty($options['TIMEOUT']) ? (int)$options['TIMEOUT'] : 3;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        // 设置只解析IPV4
        curl_setopt($ch, CURLOPT_IPRESOLVE, \CURL_IPRESOLVE_V4);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // 处理dns秒级信号丢失问题
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        
        // 设置COOKIE
        if (!empty($options['COOKIE'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $options['COOKIE']);
        }
        
        // POST请求
        // 注意：这里默认GET请求的参数附带在URL里，如果直接使用http::curl()方法，并且传data参数，会触发POST请求
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        // 设置返回头部
        if (!empty($options['HEADER'])) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        //暂无针对CURLINFO_HEADER_OUT的设置
        // 设置其他参数
        foreach ($options as $option => $val) {
            if (isset(self::SKIP_OPTIONS[$option])) {
                continue;
            }
            $opt_defined = self::$curlOptionMap[$option] ?? (defined($constant_name = 'CURLOPT_' . $option) ? self::$curlOptionMap[$option] = constant($constant_name) : true);
            if ($opt_defined === true) {
                self::$curlOptionMap[$option] = true;
            } elseif ($opt_defined !== null) {
                curl_setopt($ch, $opt_defined, $val);
            }
        }
        $response = curl_exec($ch);
        self::$lastRequestInfo = [
            'errno' => curl_errno($ch),
            'error' => curl_error($ch),
            'info'  => curl_getinfo($ch),
        ];
        
        curl_close($ch);
        return $response;
    }
    
    /**
     * @return array|null
     */
    public static function getLastRequestInfo(): ?array {
        return self::$lastRequestInfo;
    }
    
    /**
     * @param null $headers
     * @return array|null
     * @throws \Exception
     */
    public static function formatRequestHeaders($headers = null): ?array
    {
        if (!\is_array($headers)) {
            if (\is_object($headers)) {
                $headers = (array)$headers;
            } else {
                $headers = [];
            }
        }
        
        return $headers;
    }
    
    
}
