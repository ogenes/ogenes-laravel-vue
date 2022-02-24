<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/4
 */

namespace App\Helpers;


use OSS\Core\OssException;
use OSS\OssClient;

class OssHelper
{

    protected $accessKeyId;
    protected $accessKeySecret;
    protected $endpoint;
    protected $bucket;
    protected $cdnDomain;

    /**
     * OssHelper constructor.
     * @throws OssException
     */
    private function __construct()
    {
        $this->accessKeyId = config('aliyun.oss.accessKeyId');
        $this->accessKeySecret = config('aliyun.oss.accessKeySecret');
        $this->endpoint = config('aliyun.oss.endpoint');
        $this->bucket = config('aliyun.oss.bucket');
        $this->cdnDomain = config('aliyun.oss.cdnDomain');
        if (!($this->accessKeyId && $this->accessKeySecret)) {
            throw new OssException('accessKeyId or accessKeySecret is empty');
        }
    }

    /**
     * @var array
     */
    protected static array $instance = [];

    /**
     * @return static
     * @throws OssException
     */
    public static function getInstance()
    {
        $className = static::class;
        if (isset(self::$instance[$className])) {
            return self::$instance[$className];
        }
        self::$instance[$className] = new OssHelper();
        return self::$instance[$className];
    }

    /**
     * @return mixed
     * @throws OssException
     */
    public function getOssClient()
    {
        $className = OssClient::class;
        if (isset(self::$instance[$className])) {
            return self::$instance[$className];
        }
        self::$instance[$className] = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        return self::$instance[$className];

    }

    /**
     * @param string $filePath
     * @param string $objectId
     * @return string
     * @throws OssException
     */
    public function uploadAli(string $filePath, string $objectId): string
    {
        $ossClient = $this->getOssClient();
        $resp = $ossClient->uploadFile($this->bucket, $objectId, $filePath);
        $info = $resp['info'] ?? [];
        return $info['url'] ?? '';
    }

}
