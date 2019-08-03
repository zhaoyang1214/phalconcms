<?php
/**
 * @desc 全局配置文件
 * @author ZhaoYang
 * @date 2018年5月3日 下午7:54:47
 */
return [ 
    // 应用配置
    'application' => [ 
        'debug' => [ 
            'state' => true,
            'path' => BASE_PATH . 'runtime/debug/{Y-m-d-H}.log'
        ],
        'error' => [ 
            'path' => BASE_PATH . 'runtime/error/{Y-m-d-H}.log'
        ],
        // 自动加载
        'loader' => [ 
            // 文件扩展名
            'extensions' => [ 
                'php'
            ],
            'classes' => [ 
                'Smarty' => BASE_PATH . 'library/vendors/smarty/Smarty.class.php',
                'PSCWS4' => BASE_PATH . 'library/vendors/pscws4/pscws4.class.php',
                'Captcha' => BASE_PATH . 'library/vendors/captcha/Captcha.php',
            ],
            'namespaces' => [ 
                'Models' => BASE_PATH . 'models/',
                'Common' => BASE_PATH . 'common/',
                'Library\\Adapter' => BASE_PATH . 'library/adapter/',
                'Library\\Extensions' => BASE_PATH . 'library/extensions/',
                'Library\\Plugins' => BASE_PATH . 'library/plugins/',
                'Library\\Tools' => BASE_PATH . 'library/tools/',
                'Library\\Translate' => BASE_PATH . 'library/translate/',
                'Library\\Translate\\Adapter' => BASE_PATH . 'library/translate/adapter/',
                'Library\\Validators' => BASE_PATH . 'library/validators/',
                'Library\\Vendors\\Pinyin' => BASE_PATH . 'library/vendors/pinyin/src/'
            ],
            'files' => [ 
                BASE_PATH . 'library/vendors/htmlpurifier/HTMLPurifier.auto.php'
            ],
            'directories' => [ 
            ]
        ]
    ],
    // 服务配置
    'services' => [ 
        // mysql数据库配置
        'db' => [ 
            // 是否记录执行的mysql语句
            'logged' => true,
            // 记录执行时间超过0秒的mysql语句
            'max_execute_time' => 0,
            // 比较时间到小数点后几位
            'scale' => 5,
            'log_path' => BASE_PATH . 'runtime/mysql/{Y-m/d}/{Y-m-d-H}.log',
            // 使用动态更新
            'use_dynamic_update' => true,
            // ORM选项配置
            'orm_options' => [ 
                // 是否对字段是否为空的判断
                'not_null_validations' => false
            ],
            'prefix' => 'ph_',
            'mysql' => [ 
                'host' => '127.0.0.1',
                'port' => 3306,
                'username' => 'root',
                'password' => '123456',
                'dbname' => 'phalconcms',
                'charset' => 'utf8'
            ]
        ],
        // 调度器配置
        'dispatcher' => [ 
            // 处理 Not-Found错误配置
            'notfound' => [ 
                // 错误码及错误提示
                'status_code' => 404,
                'message' => 'Not Found',
                // 错误跳转的页面
                'namespace' => DEFAULT_MODULE_NAMESPACE . '\\Controllers',
                'controller' => 'error',
                'action' => 'error404'
            ]
        ],
        // volt引擎相关配置
        'view_engine_volt' => [ 
            // 编译模板目录
            'compiled_path' => BASE_PATH . 'runtime/cache/compiled/',
            // 是否实时编译
            'compile_always' => true,
            // 附加到已编译的PHP文件的扩展名
            'compiled_extension' => '.php',
            // 使用这个替换目录分隔符
            'compiled_separator' => '%%',
            // 是否要检查在模板文件和它的编译路径之间是否存在差异
            'stat' => true,
            // 模板前缀
            'prefix' => '',
            // 支持HTML的全局自动转义
            'autoescape' => false
        ],
        // smarty引擎相关配置,直接配置smarty参数
        'view_engine_smarty' => [ 
            'compile_dir' => BASE_PATH . 'runtime/cache/compiled/',
            // 一般无需配置为true,使用view_cache缓存即可
            'caching' => false,
            'cache_lifetime' => 3600,
            'cache_dir' => BASE_PATH . 'runtime/cache/view_cache/'
        ],
        // 模板相关配置
        'view' => [ 
            // 模板路径
            'view_path' => APP_PATH . DEFAULT_MODULE . '/views' . DS,
            // 模板引擎,根据模板后缀自动匹配视图引擎，不启用则设为false
            'engines' => [ 
                '.volt' => 'viewEngineVolt',
                '.phtml' => 'viewEnginePhp',
                '.html' => 'viewEngineSmarty'
            ],
            'disable_level' => [ 
                'level_action_view' => false,
                'level_before_template' => true,
                'level_layout' => true,
                'level_after_template' => true,
                'level_main_layout' => true
            ]
        ],
        // 过滤器设置
        'filter' => [ 
            // 过滤类型，支持string、trim、absint、int!、email、float、int、float!、alphanum、striptags、lower、upper、url、special_chars
            'default_filter' => 'string,trim'
        ],
        // 文件日志,formatter常用line，adapter常用file
        'logger' => [ 
            'line' => [ 
                'format' => '[%date%][%type%] %message%',
                'date_format' => 'Y-m-d H:i:s'
            ],
            'file' => [ 
                'alert' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/alert-{Y-m-d-H}.log',
                'critical' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/critical-{Y-m-d-H}.log',
                'debug' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/debug-{Y-m-d-H}.log',
                'error' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/error-{Y-m-d-H}.log',
                'emergency' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/emergency-{Y-m-d-H}.log',
                'info' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/info-{Y-m-d-H}.log',
                'notice' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/notice-{Y-m-d-H}.log',
                'warning' => BASE_PATH . 'runtime/' . DEFAULT_MODULE . '/logs/{Y-m/d}/warning-{Y-m-d-H}.log'
            ]
        ],
        // session配置
        'session' => [ 
            // 是否自动开启 SESSION
            'auto_start' => true,
            'options' => [ 
                'adapter' => 'files',
                'unique_id' => DEFAULT_MODULE
            ]
            // @formatter:off
            /* // phalcon提供了四种适配器，分别是files、memcache、redis、libmemcached
            'options' => [
                'adapter'    => 'memcache',
                'unique_id' => DEFAULT_MODULE,
                'prefix' => DEFAULT_MODULE,
                'persistent' => true,
                'lifetime' => 3600
            ],
            'options' => [
                'adapter'    => 'redis',
                'unique_id' => DEFAULT_MODULE,
                'prefix' => DEFAULT_MODULE,
                'auth' => '',
                'persistent' => false,
                'lifetime' => 3600,
                'index' => 1
            ] */
            // @formatter:on
        ],
        // 加密配置
        'crypt' => [ 
            // 加密秘钥
            'key' => DEFAULT_MODULE,
            // 填充方式，默认是0（PADDING_DEFAULT），1（PADDING_ANSI_X_923）、2（PADDING_PKCS7）、3（PADDING_ISO_10126）、4（PADDING_ISO_IEC_7816_4）、5（PADDING_ZERO）、6（PADDING_SPACE）
            'padding' => '',
            // 加密方法，默认是"aes-256-cfb"
            'cipher' => ''
        ],
        // cookies配置
        'cookies' => [ 
            // 是否使用加密,使用加密必须要设置crypt 的key值
            'use_encryption' => true
        ],
        // 缓存配置
        'cache' => [ 
            'frontend' => [ 
                // 数据处理方式，支持data（序列化）、json、base64、none、output、igbinary、msgpack
                'data' => [ 
                    'lifetime' => 86400
                ],
                'output' => [ 
                    'lifetime' => 86400
                ]
            ],
            'backend' => [ 
                // 数据缓存方式，支持memcache、file、redis、mongo、apc、apcu、libmemcached、memory、xcache
                'file' => [ 
                    'cache_dir' => BASE_PATH . 'runtime/cache/default/',
                    // 对保存的键名进行md5加密
                    'safekey' => true,
                    'prefix' => ''
                ],
                'memcache' => [ 
                    'host' => 'localhost',
                    'port' => '11211',
                    'persistent' => false,
                    'prefix' => '',
                    // 默认情况下禁用对缓存键的跟踪
                    'stats_key' => ''
                ],
                'redis' => [ 
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'auth' => '',
                    'persistent' => false,
                    'prefix' => '',
                    'stats_key' => '',
                    'index' => 0
                ]
            ]
        ],
        // 模型元数据缓存配置
        'models_metadata' => [ 
            'options' => [ 
                // 适配器，默认使用memory(内存),还支持apc、apcu、files、libmemcached、memcache、redis、session、xcache
                'adapter' => 'files',
                'meta_data_dir' => BASE_PATH . 'runtime/cache/models_metadata/'
            ],
            // @formatter:off
            /* 'options' => [
                'adapter' => 'files',
                'meta_data_dir' => BASE_PATH . 'runtime/cache/models_metadata/'
            ],
            'options' => [
                'adapter'    => 'memcache',
                'unique_id' => '',
                'prefix' => '',
                'persistent' => true,
                'lifetime' => 3600
            ],
            'options' => [
                'adapter' => 'memory',
            ],
            'options' => [
                'adapter'    => 'redis',
                'unique_id' => '',
                'prefix' => 'models_metadata_',
                'persistent' => false,
                'lifetime' => 3600,
                'stats_key' => '_PHCM_MM',
                'index' => 1
            ],
            'options' => [
                'adapter' => 'session',
                'prefix' => '',
            ] */
            // @formatter:on
        ],
        // 模型缓存配置
        'models_cache' => [ 
            'frontend' => [ 
                'adapter' => 'data',
                'lifetime' => 86400
            ],
            'backend' => [ 
                'adapter' => 'file',
                'safekey' => false,
                'prefix' => 'models_cache_',
                'cache_dir' => BASE_PATH . 'runtime/cache/models_cache/'
            ]
        ],
        // 视图缓存配置
        'view_cache' => [ 
            'frontend' => [ 
                'adapter' => 'output',
                'lifetime' => 86400
            ],
            'backend' => [ 
                'adapter' => 'file',
                'prefix' => 'view_cache_',
                // 不同模块视图缓存应存放不同目录或使用prefix，否则易造成冲突
                // 强烈建议在方法中指定视图缓存键名
                'cache_dir' => BASE_PATH . 'runtime/cache/view_cache/',
            ]
        ],
        // 数据缓存
        'data_cache' => [
            'frontend' => [
                'adapter' => 'data',
                // 如果使用的生存时间不是配置文件的时间，建议在get、save、exists等方法中加上设置时间,因为backend-adapter对时间处理有差别
                'lifetime' => 86400
            ],
            'backend' => [
                'adapter' => 'file',
                'safekey' => false,
                'prefix' => 'data_cache_',
                'cache_dir' => BASE_PATH . 'runtime/cache/data_cache/'
            ]
        ],
        // url配置
        'url' => [ 
            'base_uri' => '/',
            'static_base_uri' => '/',
            'base_path' => ''
        ],
        'flash' => [ 
            // 消息class属性值
            'css_classes' => [ 
                'error' => 'alert alert-danger',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info',
                'warning' => 'alert alert-warning'
            ],
            // 是否在生成的html中设置自动转义模式
            'autoescape' => true,
            // 是否必须使用HTML隐式格式化输出
            'automatic_html' => false,
            // 是否立即输出，为true时，调用$this->flash->message()或其他设置消息(例如success)时，消息立即输出(echo)
            // 为false时，消息不会输出，会保存在flash对象中并返回消息$res = $this->flash->success('my message');
            'implicit_flush' => false
        ],
        'flash_session' => [ 
            // 消息class属性值
            'css_classes' => [ 
                'error' => 'alert alert-danger',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info',
                'warning' => 'alert alert-warning'
            ],
            // 是否在生成的html中设置自动转义模式
            'autoescape' => true,
            // 是否必须使用HTML隐式格式化输出
            'automatic_html' => false,
            // 是否立即输出，必须设为true（默认为true），否则调用->output()不输出
            'implicit_flush' => true
        ],
        // 安全配置
        'security' => [ 
            // 设置由openssl伪随机生成器生成的字节数
            'random_bytes' => 16,
            // 设置默认hash,0=7(CRYPT_BLOWFISH_Y),1(CRYPT_STD_DES),2(CRYPT_EXT_DES),3(CRYPT_MD5),4(CRYPT_BLOWFISH),5(CRYPT_BLOWFISH_A),6(CRYPT_BLOWFISH_X),8(CRYPT_SHA256),9(CRYPT_SHA512)
            'default_hash' => 7,
            'work_factor' => 8
        ]
    ],
    'system' => [
        'version' => '1.0.0',
        // 网站名称
        'sitename' => '',
        // 网站副标题
        'seoname' => '',
        // 网站域名
        'siteurl' => '',
        // 站点关键词
        'keywords' => '',
        // 站点描述
        'description' => '',
        // 站长邮箱
        'masteremail' => '',
        // 版权信息
        'copyright' => '',
        // 备案号
        'beian' => '',
        // 客服电话
        'telephone' => '',
        // 联系人
        'linkman' => '',
        // 传真
        'fax' => '',
        // QQ
        'qq' => '',
        // 地址
        'addr' => '',
        // 静态页面缓存
        'html_cache_on' => 0,
        // 首页更新时间
        'html_index_cache_time' => '7200',
        // 其他页更新时间
        'html_other_cache_time' => '86400',
        // 搜索更新时间
        'html_search_cache_time' => '3600',
        // 数据库缓存
        'data_cache_on' => 0,
        // 模板缓存
        'tpl_cache_on' => 0,
        // 多国语言
        'language_status' => 0,
        // 开启手机版
        'mobile_open' => 0,
        // 手机版域名
        'mobile_domain' => '',
        // 手机版绑定的模板路径
        'mobile_views' => 'mobile',
        // 模板主题，针对views下
        'theme' => 'zh',
        // 首页模板
        'index_tpl' => 'index/index',
        // 搜索模板
        'search_tpl' => 'search/index',
        // TAG主页模板
        'tags_index_tpl' => 'tags/index',
        // TAG详情页模板
        'tags_info_tpl' => 'tags/info',
        // 搜索结果分页数
        'tpl_seach_page' => 20,
        // TAG主页分页数
        'tpl_tags_index_page' => 20,
        // TAG内容分页数
        'tpl_tags_page' => 20,
        // 上传文件开关
        'upload_switch' => 0,
        // 上传文件大小
        'file_size' => 2,
        // 批量上传数
        'file_num' => 10,
        // 上传图片格式
        'image_type' => 'png,jpg,jpeg,gif,bmp',
        // 上传视频格式
        'video_type' => 'flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid',
        // 上传文件格式
        'file_type' => 'png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md,xml',
        // 默认缩图开关
        'thumbnail_switch' => 0,
        // 默认缩图方式，1：裁剪；2：按比例
        'thumbnail_cutout' => 1,
        // 缩图尺寸--最大宽度
        'thumbnail_maxwidth' => 210,
        // 缩图尺寸--最大高度
        'thumbnail_maxheight' => 110,
        // 水印开关
        'watermark_switch' => 0,
        // 水印位置
        'watermark_place' => 0,
        // 水印图片
        'watermark_image' => ''
    ]
];