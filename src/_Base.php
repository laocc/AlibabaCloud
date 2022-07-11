<?php
declare(strict_types=1);

namespace laocc\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use esp\core\Library;

abstract class _Base extends Library
{
    protected $accessKeyId;
    protected $accessKeySecret;
    protected $regionID = 'cn-shanghai';
    protected $debug = false;
    protected $timeout = 3;
    protected $version = 'v20150109';
    protected $pageIndex = 1;
    protected $pageSize = 100;
    protected $conf;

    public function _init(array $option = [])
    {
        $this->accessKeyId = $option['id'] ?? null;
        if (!$this->accessKeyId and isset($option['key'])) $this->accessKeyId = $option['key'];
        else if (!$this->accessKeyId and isset($option['keyid'])) $this->accessKeyId = $option['keyid'];
        if (!$this->accessKeyId) throw new \Error('aliyun sms keyid 不能为空');

        $this->accessKeySecret = $option['secret'] ?? null;
        if (!$this->accessKeySecret) throw new \Error('aliyun sms secret 不能为空');

        $this->conf = $option;
        if (isset($option['regionID'])) $this->regionID = strval($option['regionID']);
        if (isset($option['debug'])) $this->debug = boolval($option['debug']);
        if (isset($option['timeout'])) $this->timeout = intval($option['timeout']);
        if (isset($option['index'])) $this->pageIndex = intval($option['index']);
        if (isset($option['size'])) $this->pageSize = intval($option['size']);

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