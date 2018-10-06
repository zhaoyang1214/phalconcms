# 这是一个使用phalcon开发的内容管理系统
##特点：
1. 丰富完善的权限管理机制
2. 支持多国语言
3. 支持栏目扩展
4. 支持移动版和指定模板主题
5. 支持模型缓存、模板缓存、页面缓存
6. 支持批量上传、缩略图、水印等
7. 支持内容审核机制
8. 自动生成表单功能
9. 支持自定义变量、内容替换、tag、推荐位等
10. 支持智能翻译、翻译纠正等

####详见博客：https://blog.csdn.net/u014691098，关于该cms的使用教程将会在此博客陆续发布

##使用
######1、安装phalcon扩展，可参照https://blog.csdn.net/u014691098/article/details/80169298

######2、将代码上传，将runtime目录和public/uploads添加可写权限

######3、创建数据库，执行phalconcms.sql，修改config/config_dev.php和config_pro.php的数据库配置

######4、访问 域名/admin即可进入后台，默认账户有admin和phalcon，密码均为123456，建议登录账户后修改密码

######5、默认使用的是开发版配置（config_dev.php）,可修改config/define.php，将'NOW_ENV' 的值设为'pro'


