<?php

namespace laocc\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\RKvstore\RKvstore;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Redis
{
    public function load()
    {
        try {
            $result = RKvstore::v20150101()
                ->describeInstances()
                ->request()
                ->toArray();

            return $result;
        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }
}