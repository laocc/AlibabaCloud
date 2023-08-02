<?php
declare(strict_types=1);

namespace laocc\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use esp\error\Error;
use esp\core\Library;

abstract class _Base extends Library
{
    protected string $accessKeyId;
    protected string $accessKeySecret;
    protected string $regionID = 'cn-shanghai';
    protected bool $debug = false;
    protected int $timeout = 3;
    protected string $version = 'v20150109';
    protected int $pageIndex = 1;
    protected int $pageSize = 100;
    protected array $conf;

    public function _init(array $option = [])
    {
        $accessKeyId = $option['id'] ?? '';

        if (!$accessKeyId and isset($option['key'])) $accessKeyId = $option['key'];
        else if (!$accessKeyId and isset($option['keyid'])) $accessKeyId = $option['keyid'];
        if (!$accessKeyId) throw new Error('aliyun accessKeyId 不能为空');

        $accessKeySecret = $option['secret'] ?? '';
        if (!$accessKeySecret) throw new Error('aliyun accessKeySecret 不能为空');

        $this->conf = $option;
        if (isset($option['regionID'])) $this->regionID = strval($option['regionID']);
        if (isset($option['debug'])) $this->debug = boolval($option['debug']);
        if (isset($option['timeout'])) $this->timeout = intval($option['timeout']);
        if (isset($option['index'])) $this->pageIndex = intval($option['index']);
        if (isset($option['size'])) $this->pageSize = intval($option['size']);

        $this->setClient($accessKeyId, $accessKeySecret);
    }

    public function setClient(string $keyID, string $secret): _Base
    {
        $this->accessKeyId = $keyID;
        $this->accessKeySecret = $secret;

        AlibabaCloud::accessKeyClient($keyID, $secret)->regionId($this->regionID)->asDefaultClient();

        return $this;
    }


    public function setRegionId(string $regionID): _Base
    {
        $this->regionID = $regionID;
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)->regionId($regionID)->asDefaultClient();
        return $this;
    }

    public function setTimeout(int $timeout): _Base
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setPage(int $size, int $index = 1): _Base
    {
        $this->pageIndex = $index;
        $this->pageSize = $size;
        return $this;
    }


}