<?php
// 模块名称
define('MODULE_NAME', 'admin');
// 模块命名空间
define('MODULE_NAMESPACE', APP_NAMESPACE . '\\Admin');

return [ 
    // 应用配置
    'application' => [ 
        'debug' => [ 
            'state' => true,
            'path' => '' 
        ],
        'loader' => [ 
            'namespaces' => [ 
                MODULE_NAMESPACE . '\\Controllers' => APP_PATH . MODULE_NAME . '/controllers/',
            ],
            'files' => [
            ]
        ]
    ],
    // 服务配置
    'services' => [ 
        // 调度器配置
        'dispatcher' => [ 
            // 模块默认的命名空间
            'module_default_namespaces' => MODULE_NAMESPACE . '\\Controllers',
            // 处理 Not-Found错误配置
            'notfound' => [ 
                // 错误码及错误提示
                'status_code' => 404,
                'message' => 'Not Found',
                // 错误跳转的页面
                'namespace' => MODULE_NAMESPACE . '\\Controllers',
                'controller' => 'error',
                'action' => 'error404'
            ]
        ],
        // 模板相关配置
        'view' => [ 
            // 模板路径
            'view_path' => APP_PATH . MODULE_NAME . '/views/zh/',
            'disable_level' => [ 
                'level_action_view' => false,
                'level_before_template' => false,
                'level_layout' => true,
                'level_after_template' => true,
                'level_main_layout' => true
            ]
        ],
        // 文件日志,formatter常用line，adapter常用file
        'logger' => [ 
            'line' => [ 
                'format' => '[%date%][%type%] %message%',
                'dateFormat' => 'Y-m-d H:i:s'
            ],
            'file' => [ 
                'alert' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/alert-{Y-m-d-H}.log',
                'critical' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/critical-{Y-m-d-H}.log',
                'debug' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/debug-{Y-m-d-H}.log',
                'error' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/error-{Y-m-d-H}.log',
                'emergency' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/emergency-{Y-m-d-H}.log',
                'info' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/info-{Y-m-d-H}.log',
                'notice' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/notice-{Y-m-d-H}.log',
                'warning' => BASE_PATH . 'runtime/' . MODULE_NAME . '/logs/{Y-m/d}/warning-{Y-m-d-H}.log'
            ]
        ],
        'session' => [ 
            'auto_start' => true,
            'options' => [ 
                'adapter' => 'files',
                'unique_id' => MODULE_NAME
            ]
        ],
        // 加密配置
        'crypt' => [ 
            // 加密秘钥
            'key' => MODULE_NAME,
            // 填充方式，默认是0（PADDING_DEFAULT），1（PADDING_ANSI_X_923）、2（PADDING_PKCS7）、3（PADDING_ISO_10126）、4（PADDING_ISO_IEC_7816_4）、5（PADDING_ZERO）、6（PADDING_SPACE）
            'padding' => '',
            // 加密方法，默认是"aes-256-cfb"
            'cipher' => ''
        ],
        // url配置
        'url' => [ 
            'base_uri' => '/' . MODULE_NAME . '/',
            'static_base_uri' => '/' . MODULE_NAME . '/static/zh/',
            'base_path' => ''
        ]
    ]
];