<?php
declare(strict_types=1);

namespace laocc\aliyun;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use AlibabaCloud\Ecs\Ecs;

class Service extends _Base
{
    public function load(string $RegionId = null)
    {
        //->withRegionId($RegionId)
        try {
            $service = [];
            $result = Ecs::v20140526()->describeInstances()
                ->debug($this->debug)
                ->connectTimeout($this->timeout)
                ->timeout($this->timeout)
                ->request()->toArray();
            foreach ($result['Instances']['Instance'] as $sv) {
                $new = [
                    'id' => $sv['InstanceId'],
                    'os' => $sv['OSName'],
                    'cpu' => $sv['Cpu'],
                    'memory' => $sv['Memory'],//内存大小，单位为MiB。
                    'ip' => $sv['PublicIpAddress']['IpAddress'][0] ?? '',
                    'eip' => $sv['EipAddress']['IpAddress'] ?? '',
                    'ewide' => $sv['EipAddress']['Bandwidth'] ?? '',
                    'wide' => $sv['InternetMaxBandwidthOut'],//公网带宽最大值，单位为Mbit/s。
                    'zone' => $sv['ZoneId'],
                    'room' => $sv['RegionId'],
                    'create' => $sv['CreationTime'],
                    'start' => $sv['StartTime'],
                    'expire' => $sv['ExpiredTime'],
                    'status' => $sv['Status'],
                    'name' => $sv['HostName'],
                    'desc' => $sv['InstanceName'],
                ];
                if ($sv['InternetMaxBandwidthIn'] !== $new['wide']) {
                    $new['wide'] = "{$sv['InternetMaxBandwidthIn']}/{$new['wide']}";
                }
                $service[] = $new;
            }

            return $service;


        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }
}