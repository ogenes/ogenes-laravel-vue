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
    
    protected OssClient $client;
    
    /**
     * OssHelper constructor.
     * @throws OssException
     */
    public function __construct()
    {
        $this->accessKeyId = config('param.alioss.accessKeyId');
        $this->accessKeySecret = config('param.alioss.accessKeySecret');
        $this->endpoint = config('param.alioss.endpoint');
        $this->bucket = config('param.alioss.bucket');
        $this->cdnDomain = config('param.alioss.cdnDomain');
        if (!($this->accessKeyId && $this->accessKeySecret)) {
            throw new OssException('accessKeyId or accessKeySecret is empty');
        }
        $this->client = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
    }
    
    /**
     * @param string $filePath
     * @param string $objectId
     * @return string
     * @throws OssException
     */
    public function upload(string $filePath, string $objectId): string
    {
        $resp = $this->client->uploadFile($this->bucket, $objectId, $filePath);
        $info = $resp['info'] ?? [];
        return $info['url'] ?? '';
    }
    
}
