<?php
/**
 * @desc 翻译插件接口
 * @author: ZhaoYang
 * @date: 2018年7月24日 上午12:32:54
 */
namespace Library\Translate;

interface AdapterInterface {

    /**
     * @desc 定义需要配置的参数
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午12:33:18
     */
    public static function needSetConfig(): array;

    /**
     * @desc 定义基础支持语言
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午12:33:31
     */
    public static function baseLanguage(): array;

    /**
     * @desc 发送请求翻译
     * @param string $q 待翻译字符串
     * @param string $from 翻译源语言
     * @param string $to 译文语言
     * @return: string|bool
     * @author: ZhaoYang
     * @date: 2018年7月24日 上午12:34:09
     */
    public function query(string $q, string $from, string $to);
}