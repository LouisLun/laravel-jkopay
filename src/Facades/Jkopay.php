<?php
namespace LouisLun\LaravelJkopay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \LouisLun\LaravelJkopay\Response request($params) 向街口請求付款
 * @method static \LouisLun\LaravelJkopay\Response refund($params) 退款
 * @method static \LouisLun\LaravelJkopay\Response details($params) 查詢交易紀錄
 *
 * @see \LouisLun\LaravelJkopay\Jkopay
 */
class Jkopay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \LouisLun\LaravelJkopay\Jkopay::class;
    }
}
