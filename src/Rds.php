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
            $result = AlibabaCloud::rpc()
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
            $rdsAll = [];
            $this->debug($result['Items']['DBInstance']);

            foreach ($result['Items']['DBInstance'] as $rs) {
                $rds = [];
                $rds['mid'] = $rs['MasterInstanceId'] ?? '';//主实例ID
                $rds['id'] = $rs['DBInstanceId'];//实例id
                $rds['stype'] = $rs['DBInstanceStorageType'] ?? '';//存储类型
                $rds['lock'] = $rs['LockMode'];
                $rds['create'] = $rs['CreateTime'];
                $rds['expire'] = $rs['ExpireTime'];
                $rds['paytype'] = $rs['PayType'];
                $rds['version'] = $rs['EngineVersion'];
                $rds['host'] = $rs['ConnectionString'];//连接地址
                $rds['nettype'] = $rs['DBInstanceNetType'];//连接地址
                $rds['zone'] = $rs['ZoneId'];//可用区ID。 cn-hangzhou-a
                $rds['region'] = $rs['RegionId'];//地域ID。 cn-hangzhou
                $rds['desc'] = $rs['DBInstanceDescription'] ?? '';
                $rds['itype'] = $rs['DBInstanceType'];//实例类型Primary：主实例，ReadOnly：只读实例
                $rds['class'] = $rs['DBInstanceClass'];//规格
                $rds['type'] = $rs['Engine'];//类型 mysql
                $rds['category'] = $rs['Category'] ?? '';//实例系列： Basic：基础版HighAvailability：高可用版Finance：三节点企业版
                $rds['status'] = $rs['DBInstanceStatus'];
                $rdsAll[] = $rds;
            }
            return $rdsAll;
        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }
}