<?php

namespace laocc\aliyun;


use AlibabaCloud\Client\AlibabaCloud;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Rds extends _Base
{
    /**
     * @return array|string
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     */
    public function load()
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
             * ],
             * ])
             * ->request();
             */
            return AlibabaCloud::rpc()
                ->product('Rds')
                ->version('2014-08-15')
                ->action('DescribeDBInstances')
                ->method('POST')
                ->host('rds.aliyuncs.com')
                ->options([
                    'query' => [],
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