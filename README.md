# 高可用 laravel

## 安装

`composer create-project --prefer-dist laravel/laravel laravel`



## 常用拓展

[Laravel 优质扩展包](https://xueyuanjun.com/books/laravel-packages)  [Laravel 入门到精通教程](https://xueyuanjun.com/books/laravel-tutorial)



### jwt-auth

[jwt-auth](https://jwt-auth.readthedocs.io/en/develop/) 是一个非常轻巧的规范。这个规范允许我们使用JWT在用户和服务器之间传递安全可靠的信息。

1. `composer require tymon/jwt-auth`
2. `php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"`
3. `php artisan jwt:secret`
4. 后续可以使用推荐的 `AuthController` ，也可以自定义验证再调用提供的方法就行。

> [JWT 完整使用详解](https://learnku.com/articles/10885/full-use-of-jwt)  [JWT](https://learnku.com/articles/6216/laravel-uses-jwt-to-implement-api-auth-to-build-user-authorization-interfaces)  [JWT 超详细分析](https://learnku.com/articles/17883)



### Dingo Api

[Dingo API](https://github.com/dingo/api) 提供了一整套工具以便帮助开发者快速构建遵循 REST 规范的 API 接口。

1. `composer require dingo/api`
2. `php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"`

> 用了下，感觉不好用，直接用原生的吧！
>



### laravel-permission

[Laravel-permission](https://spatie.be/docs/laravel-permission/v3/introduction)  allows you to manage user permissions and roles in a database.

1. `composer require spatie/laravel-permission`
2. `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
3. ` php artisan migrate`
4. 知道数据库对应的作用 && 熟记几个常用的验证方法



### phpspreadsheet

[phpspreadsheet](https://phpspreadsheet.readthedocs.io/en/latest/)  is a library written in pure PHP and offers a set of classes that allow you to read and write various spreadsheet file formats such as Excel and LibreOffice Calc.

1. `composer require phpoffice/phpspreadsheet`
2. 大数据导出，看代码 （淦！）

> [ PhpSpreadSheet 使用总结](https://learnku.com/articles/29608)
>
> [maatwebsite/excel](https://docs.laravel-excel.com/3.1/getting-started/)  是对 phpspreadsheet 的再封装，增加了一些新的特性



### laravel-websockets

[laravel-websockets](https://packagist.org/packages/beyondcode/laravel-websockets)  is a package for Laravel 5.7 and up that will get your application started with WebSockets in no-time! It has a drop-in Pusher API replacement, has a debug dashboard, realtime statistics and even allows you to create custom WebSocket controllers.

1. `composer require beyondcode/laravel-websockets`
2. `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"`
3. `php artisan migrate`
4. `php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"`

更喜欢用 socket.io + redis



### Socket.io

1. 服务端使用 `laravel-echo-server`

2. 客户端使用 `laravel-echo`

   > 注意 "socket.io-client" 版本要和服务器版本保持一致，血泪教训



### 全文搜索

- 付费套件

    - `algolia/algoliasearch-client-php`
    - `laravel/scout`

- 免费套件

    - `elasticsearch/elasticsearch`  👍👍👍

      > 也可以只使用这个接口

    - `laravel/scout`

    - `tamayo/laravel-scout-elastic`

        - 很简洁，一些功能需要自己去开发

    - `babenkoivan/scout-elasticsearch-driver`  推荐使用

        - [scout-elasticsearch-driver](https://babenkoivan.github.io/scout-elasticsearch-driver/#configuration)
        - 支持定制 mapping ......



## 默认安装的

### fideloper/proxy

[fideloper/proxy](https://packagist.org/packages/fideloper/proxy)  Setting a trusted proxy allows for correct URL generation, redirecting, session handling and logging in Laravel when behind a reverse proxy such as a load balancer or cache.



### fruitcake/laravel-cors

[fruitcake/laravel-cors](https://packagist.org/packages/fruitcake/laravel-cors)  This package allows you to send [Cross-Origin Resource Sharing](http://enable-cors.org/) headers with Laravel middleware configuration.



### guzzlehttp/guzzle

[guzzlehttp/guzzle](https://docs.guzzlephp.org/en/stable/)  Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with web services.



## 帮助开发的

### laravel-ide-helper

[laravel-ide-helper](https://packagist.org/packages/barryvdh/laravel-ide-helper)  This package generates helper files that enable your IDE to provide accurate autocompletion. Generation is done based on the files in your project, so they are always up-to-date.

1. `composer require --dev barryvdh/laravel-ide-helper`
2. 三种用法
    - `php artisan ide-helper:generate`  为 Facades 生成注释
    - `php artisan ide-helper:models`  为数据模型生成注释
    - `php artisan ide-helper:meta`  生成 PhpStorm Meta file

> [IDE Helper 使用](https://xueyuanjun.com/post/4202)  



