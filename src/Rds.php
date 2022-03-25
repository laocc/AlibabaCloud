<?php

namespace laocc\aliyun;


use AlibabaCloud\Client\AlibabaCloud;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Rds extends _Base
{
    public function load(string $RegionId)
    {
        try {
            /**
             * AlibabaCloud::rpc()
             * ->product('Rds')
             * // ->scheme('https') // https | http
             * ->version('2014-08-15')
             * ->action('DescribeDBInstances')
             * ->method('POST')
             * ->host('rds.aliyuncs.com')
             * ->options([
             * 'query' => [
             *
             * ],
             * ])
             * ->request();
             */
            return AlibabaCloud::rpc()
                ->product('Rds')
                ->version('2014-08-15')
                ->action('DescribeDBInstances')
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