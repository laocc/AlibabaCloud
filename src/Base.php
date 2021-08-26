<?php

namespace laocc\aliyun;


use AlibabaCloud\Client\AlibabaCloud;

abstract class Base
{
    protected $accessKeyId;
    protected $accessKeySecret;
    protected $regionID = 'cn-shanghai';
    protected $debug = false;
    protected $timeout = 3;
    protected $version = 'v20150109';
    protected $pageIndex = 1;
    protected $pageSize = 10;

    public function __construct(array $option = [])
    {
        $this->accessKeyId = $option['key'] ?? null;
        $this->accessKeySecret = $option['secret'] ?? null;
        if (isset($option['regionID'])) $this->regionID = strval($option['regionID']);
        if (isset($option['debug'])) $this->debug = boolval($option['debug']);
        if (isset($option['timeout'])) $this->timeout = intval($option['timeout']);
        if (isset($option['index'])) $this->timeout = intval($option['index']);
        if (isset($option['size'])) $this->timeout = intval($option['size']);

        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
            ->regionId($this->regionID)->asDefaultClient();
    }

    public function setClient(string $keyID, string $secret)
    {
        $this->accessKeyId = $keyID;
        $this->accessKeySecret = $secret;

        AlibabaCloud::accessKeyClient($keyID, $secret)
            ->regionId('cn-shanghai')->asDefaultClient();

        return $this;
    }

    public function regionId(string $regionID)
    {
        $this->regionID = $regionID;
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
            ->regionId($regionID)->asDefaultClient();
        return $this;
    }

    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setPage(int $size, int $index = 1)
    {
        $this->pageIndex = $index;
        $this->pageSize = $size;
        return $this;
    }


}