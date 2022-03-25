<?php
declare(strict_types=1);

namespace laocc\aliyun;

use AlibabaCloud\BssOpenApi\BssOpenApi;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Boss extends _Base
{
    public function balance()
    {
        try {
            $acc = BssOpenApi::v20171214()->queryAccountBalance()
                ->request()
                ->toArray();
            return floatval($acc['Data']['AvailableAmount']);
            /**
             * [AvailableCashAmount] => 193.04 可用额度。
             * [MybankCreditAmount] => 0.00网商银行信用额度。
             * [Currency] => CNY
             * [AvailableAmount] => 193.04 现金余额。
             * [CreditAmount] => 0.00信控余额。
             */
        } catch (ClientException $exception) {
            return $exception->getMessage();
        } catch (ServerException $exception) {
            return $exception->getMessage();
        }

    }
}