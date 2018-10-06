<?php
/**
 * @desc 基础工具类
 * @author ZhaoYang
 * @date 2018年5月4日 下午11:21:11
 */
namespace Common;

use Phalcon\Text;

class Common {

    /**
     * @desc 格式化目录
     * @author ZhaoYang
     * @date 2018年5月4日 下午11:21:27
     */
    public static function dirFormat(string $path) {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace_callback('/(\{.+\})/U', function ($matches) {
            return date(rtrim(ltrim($matches[0], '{'), '}'));
        }, $path);
        return $path;
    }

    /**
     * @desc 创建目录
     * @param string $pathname 路径
     * @param int $mode 文件夹权限默认情况下，模式是0777
     * @param bool $recursive 规定是否设置递归模式
     * @param resource $context 规定文件句柄的环境。Context 是可修改流的行为的一套选项。
     * @return bool
     * @author ZhaoYang
     * @date 2018年5月4日 下午11:21:43
     */
    public static function mkdir(string $pathname, int $mode = 0777, bool $recursive = true, $context = null) {
        if (empty($pathname)) {
            return false;
        }
        if (is_dir($pathname)) {
            return true;
        }
        return is_resource($context) ? mkdir($pathname, $mode, $recursive, $context) : mkdir($pathname, $mode, $recursive);
    }

    /**
     * @desc 将数组中键名下划线转换为驼峰
     * @param array|object $arr
     * @param bool $lcfirst 首字母是否小写
     * @return mixed
     * @author ZhaoYang
     * @date 2018年5月4日 下午11:22:21
     */
    public static function convertArrKeyUnderline($arr, $lcfirst = true) {
        $type = is_array($arr) ? 1 : (is_object($arr) ? 2 : 0);
        if ($type) {
            foreach ($arr as $k => $v) {
                $key = $k;
                if (strpos($key, '_') !== false) {
                    $key = Text::camelize($key);
                    $lcfirst && $key = lcfirst($key);
                    if ($type == 1) {
                        unset($arr[$k]);
                    } else {
                        unset($arr->$k);
                    }
                }
                if (is_array($v) || is_object($v)) {
                    $v = static::convertArrKeyUnderline($v);
                }
                if ($type == 1) {
                    $arr[$key] = $v;
                } else {
                    $arr->$key = $v;
                }
            }
        }
        return $arr;
    }

    /**
     * @desc 删除目录及文件
     * @param string $dirPath 目录路径
     * @param bool $delDir 是否删除该目录
     * @param bool $recursive 是否递归
     * @return: mixed
     * @author: ZhaoYang
     * @date: 2018年7月15日 下午8:51:28
     */
    public static function delDir(string $dirPath, bool $delDir = false, bool $recursive = true) {
        if (!is_dir($dirPath)) {
            return true;
        }
        $handle = opendir($dirPath);
        while (($file = readdir($handle)) !== false) {
            if(strstr(PHP_OS, 'WIN')) {
                $file = function_exists('mb_convert_encoding') ? mb_convert_encoding($file, 'UTF-8', 'GBK') : iconv('GBK', 'UTF-8', $file);
            }
            if ($file != '.' && $file != '..') {
                is_dir("$dirPath/$file") && $recursive ? self::delDir("$dirPath/$file", true, $recursive) : @unlink("$dirPath/$file");
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            $delDir && @rmdir($dirPath);
        }
    }

    /**
     * @desc 根据一组一维数组的值取另一个多维数组对应键名的值并组成新数组
     * @param array $keys 一维数组
     * @param array $array 多维数组
     * @param bool $setNull 键名在数组中不存在时是否加入返回数组
     * @return: bool|array
     * @author: ZhaoYang
     * @date: 2018年7月23日 下午9:24:04
     */
    public static function arraySlice(array $keys, array $array, bool $setNull = false) {
        if (empty($keys) || empty($array)) {
            return [ ];
        }
        $newArray = [ ];
        foreach ($keys as $key) {
            if(array_key_exists($key, $array)) {
                $newArray[$key] = $array[$key];
            } else if ($setNull) {
                $newArray[$key] = null;
            }
        }
        return $newArray;
    }
    
    /**
     * @desc 获取目录下文件
     * @param string $dirPath 目录
     * @param string $pattern 文件名匹配正则  例如：'/.+\.png$/i'
     * @param bool $recursive 是否递归
     * @return array
     * @author: ZhaoYang
     * @date: 2018年8月18日 下午11:30:27
     */
    public static function getFiles(string $dirPath, string $pattern = null, bool $recursive = true) {
        $dirPath = rtrim($dirPath, '\\/');
        if (!is_dir($dirPath)) {
            return [ ];
        }
        $handle = opendir($dirPath);
        $files = [ ];
        while (($file = readdir($handle)) !== false) {
            if(strstr(PHP_OS, 'WIN')) {
                $file = function_exists('mb_convert_encoding') ? mb_convert_encoding($file, 'UTF-8', 'GBK') : iconv('GBK', 'UTF-8', $file);
            }
            if ($file != '.' && $file != '..') {
                var_dump($file);
                $filePath = "$dirPath/$file";
                var_dump($filePath);
                if(is_dir($filePath) && $recursive) {
                    $files = array_merge($files, self::getFiles($filePath, $pattern, $recursive));
                } else if(is_null($pattern) || preg_match($pattern, $file)) {
                    $files[ ] = $filePath;
                }
            }
        }
        closedir($handle);
        var_dump($files);
        return $files;
    }
    
    /** 
     * @desc 过滤meta标签 
     * @param string $html 待过滤的字符串 
     * @return string 
     * @author ZhaoYang 
     * @date 2018年9月14日 上午11:32:39 
     */
    public static function HTMLPurifierMeta(string $html) {
        $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
        $htmlPurifierConfig->set('Core.Encoding', 'UTF-8');
        $htmlPurifierConfig->set('HTML.Allowed', 'meta[content|name|scheme|http-equiv]');
        $def = $htmlPurifierConfig->getHTMLDefinition(true);
        $def->addElement('meta', 'Inline', 'Empty', false, [
            'content' => 'CDATA',
            'name' => 'CDATA',
            'scheme' => 'CDATA',
            'http-equiv' => 'CDATA',
        ]);
        $htmlPurifier = new \HTMLPurifier($htmlPurifierConfig);
        return $htmlPurifier->purify($html);
    }
}