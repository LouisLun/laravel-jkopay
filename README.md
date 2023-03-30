# laravel-jkopay

Laravel Jkopay 為針對 Laravel 所寫的金流套件，主要實作街口付款功能

## 安裝

```
composer require louislun/laravel-linepay
```

### 註冊套件

> Laravel 5.5 以上會自動註冊套件，可以跳過此步驟

在 `config/app.php` 註冊套件和增加別名：

```php
    'providers' => [
        ...
        /*
         * Package Service Providers...
         */
        \LouisLun\LaravelJkopay\JkopayServiceProvider::class,
    ],
    'aliases' => [
        ...
        'Linepay' => \LouisLun\LaravelJkopay\Facades\Jkopay::class,
    ]
```

### 發布設置檔案

```
php artisan vendor:publish --provider="LouisLun\LaravelJkopay\JkopayServiceProvider"
```

## 使用

## License

[MIT](./LICENSE)