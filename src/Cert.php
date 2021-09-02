<?php

namespace laocc\aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Cert extends _Base
{
    public function load()
    {
        try {
            return AlibabaCloud::rpc()
                ->product('cas')
                ->version('2018-07-13')
                ->action('DescribeUserCertificateList')
                ->method('POST')
                ->host('cas.aliyuncs.com')
                ->options([
                    'query' => [
                        'ShowSize' => "100",
                        'CurrentPage' => "1",
                    ],
                ])
                ->request()
                ->toArray();

        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }
}