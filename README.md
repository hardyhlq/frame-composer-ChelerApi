# ChelerApi Framework

## 安装
- 创建一个项目目录 `mkdir project`
- `cd project`
- 先编写composer.json, 示例：
- 
```
{
  "name": "cheler/frameDemo",
  "description": "A PHP Project",
  "type": "library",
  "license": "MIT",
  "keywords": ["frame"],
  "require": {
    "php": ">=7.0.0",
    "phpunit/phpunit": "6.5.5",
    "cheler/frame": "0.0.12"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "git@git.linewin.cc:composer/ChelerApi.git",
      "name": "cheler/frame"
    }
  ],
  "autoload": {
    "psr-4": {
      "controller\\": "controller",
      "dao\\": "dao",
      "service\\": "service"
    }
  }
}
```

- 在composer.json的目录执行`composer update`
- 执行`./vendor/bin/projectLw`
- 配置nginx
- 
```
server {
    listen       80;
    server_name  你的项目名;
    index index.php;
    root 项目路径;
    location / {
        if (!-e $request_filename) {
              rewrite ^/(.*) /index.php?$1 last;
          }
    }
    location ~\.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass	127.0.0.1:9000;
        fastcgi_index	index.php;
        fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include		fastcgi_params;
    }
}
```

- 重启nginx访问你配置的域名 成功输出hello 表示安装成功

## 访问规则
`域名/版本/模块/控制器`
示例 `www.test.com/1/car/accident/info?id=1`

## 接收参数
CRequest提供四个静态属性
```
    /**
     * GET请求参数
     * @var array
     */
    public static $_GET = [];

    /**
     * POST请求参数
     * @var array
     */
    public static $_POST = [];

    /**
     * PUT请求参数
     * @var array
     */
    public static $_PUT = [];

    /**
     * DELETE请求参数
     * @var array
     */
    public static $_DELETE = [];
```
示例
```
isset(CRequest::$_GET['id'])
isset(CRequest::$_POST['id'])
isset(CRequest::$_PUT['id'])
isset(CRequest::$_DELETE['id'])
```

## 获取redis/mongo/memcached
```
$this->getMongo('mongoConfig1')
$this->getMemcache('memcacheConfig1')->get("param");
$this->getRedis('redisConfig1')->set("foo","bar");
```

## mongo
```
namespace app\dao\clw2;


use frame\runtime\MongoDao;

class MongoDemoDao extends MongoDao
{
    protected $col = "admin_log";
    protected $mongoDb = "tinyapi";
    protected $serverName = "log";//$_CONFIG_['mongo']['log']['server']     = '127.0.0.1';配置中的log

    public function insert() {
        $field = [
            'param' => ['a','b','c'],
            'startTime' => time(),
        ];

        $this->init_mongo($this->mongoDb, $this->col)->bulkWriteInster($field);
    }
}
```