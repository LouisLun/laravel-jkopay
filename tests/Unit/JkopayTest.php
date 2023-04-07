<?php
namespace LouisLun\LaravelJkopay\Tests;

use LouisLun\LaravelJkopay\Jkopay;
use PHPUnit\Framework\TestCase;

class JkopayTest extends TestCase
{
    public function test_getAuthSignature_body_format()
    {
        $this->assertEquals(
            '3577609b058ab85c2d0a00a5421a991979ed6b9f549476e9a82476dc1b70d876',
            Jkopay::getAuthSignature(
                'r0odDC1e9LHXDmxuvmOv9bgaWLf2CXB2c4gMheoFucVKNMi1K0Id9zwRHJF1r-kdtAKriKgb11VDlo7Kb8R-FQ',
                '{"platform_order_id":"demo-order-001","store_id":"35f12dff-1581-11e9-a054-00505684fd45","currency": "TWD","total_price":10,"final_price":10,"unredeem":10,"result_display_url":"https://display.com","result_url":"https://result-callback.xxx/xxx"}'
            )
        );
    }

    public function test_getAuthSignature_query_format()
    {
        $this->assertEquals(
            '7778b95890af17c5b41e8cef957f4769e7bfecc79e9f9ee555923293ebd8e880',
            Jkopay::getAuthSignature(
                'r0odDC1e9LHXDmxuvmOv9bgaWLf2CXB2c4gMheoFucVKNMi1K0Id9zwRHJF1r-kdtAKriKgb11VDlo7Kb8R-FQ',
                'platform_order_ids=test123,demo-order-001'
            )
        );
    }

    public function test_curl()
    {
        $client = new Jkopay([
            'storeID' => '14ba2382-c1bd-11ed-8c25-0050568403ed',
            'apiKey' => 'f9e22c563f31538aefc231e7d3890ac540dca5aed64afaa64c21e8b551ceaee4',
            'secretKey' => 'a40d3d181d1a4a0e01804db62f08419656639cd124187d7bee13f9ae992e81bd',
            // sandbox mode
            'isSandbox' => true,
        ]);

        $response = $client->details([
            'platform_order_ids' => [87,88],
        ]);

        dd($response->toArray());
    }
}
