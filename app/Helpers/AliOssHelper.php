<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/1/4
 */

namespace App\Helpers;


use OSS\Core\OssException;
use OSS\OssClient;

class AliOssHelper
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
        $this->accessKeyId = config('param.alioss.accessKeyId');
        $this->accessKeySecret = config('param.alioss.accessKeySecret');
        $this->endpoint = config('param.alioss.endpoint');
        $this->bucket = config('param.alioss.bucket');
        $this->cdnDomain = config('param.alioss.cdnDomain');
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
        self::$instance[$className] = new AliOssHelper();
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
    public function upload(string $filePath, string $objectId): string
    {
        $ossClient = $this->getOssClient();
        $resp = $ossClient->uploadFile($this->bucket, $objectId, $filePath);
        $info = $resp['info'] ?? [];
        return $info['url'] ?? '';
    }

}
