<?php
/**
 * @desc 有道翻译
 * @author: ZhaoYang
 * @date: 2018年7月24日 上午12:37:16
 */
namespace Library\Translate\Adapter;

use Library\Tools\HttpCurl;
use Library\Translate\AdapterInterface;

class Youdao implements AdapterInterface {

    private $url;

    private $appKey;

    private $secretKey;

    private $map;

    public function __construct(array $config = null) {
        if (!is_null($config)) {
            if (!isset($config['url']) || empty($config['url'])) {
                throw new \Exception('url 不能为空');
            }
            if (!isset($config['appKey']) || empty($config['appKey'])) {
                throw new \Exception('appKey 不能为空');
            }
            if (!isset($config['secretKey']) || empty($config['secretKey'])) {
                throw new \Exception('secretKey 不能为空');
            }
            if (!isset($config['map']) || !is_array($config['map'])) {
                throw new \Exception('map 不能为空且必须为数组');
            }
            $this->url = $config['url'];
            $this->appKey = $config['appKey'];
            $this->secretKey = $config['secretKey'];
            $this->map = $config['map'];
        }
    }

    /**
     * @desc 需要配置的参数
     * @return array
     * @author ZhaoYang
     * @date 2018年7月25日 上午10:42:13
     */
    public static function needSetConfig(): array {
        return [ 
            'url' => '翻译API请求地址',
            'appKey' => '应用申请的key',
            'secretKey' => '密钥'
        ];
    }

    /** 
     * @desc 支持的基础语言 
     * @return array 
     * @author ZhaoYang 
     * @date 2018年7月25日 上午10:42:31 
     */
    public static function baseLanguage(): array {
        return [ 
            'zh-CHS' => '中文',
            'ja' => '日文',
            'EN' => '英文',
            'ko' => '韩文',
            'fr' => '法文',
            'ar' => '阿拉伯文',
            'pl' => '波兰文',
            'da' => '丹麦文',
            'de' => '德文',
            'ru' => '俄文',
            'fi' => '芬兰文',
            'nl' => '荷兰文',
            'cs' => '捷克文',
            'ro' => '罗马尼亚文',
            'no' => '挪威文',
            'pt' => '葡萄牙文',
            'sv' => '瑞典文',
            'sk' => '斯洛伐克文',
            'es' => '西班牙文',
            'hi' => '印地文',
            'id' => '印度尼西亚文',
            'it' => '意大利文',
            'th' => '泰文',
            'tr' => '土耳其文',
            'el' => '希腊文',
            'hu' => '匈牙利文'
        ];
    }

    /** 
     * @desc 发送请求翻译
     * @param string $q 待翻译字符串
     * @param string $from 翻译源语言
     * @param string $to 译文语言
     * @return: string|bool 
     * @author ZhaoYang 
     * @date 2018年7月25日 上午10:41:46 
     */
    public function query(string $q, string $from, string $to) {
        if (empty($this->url)) {
            return false;
        }
        if (empty($q) || empty($from) || empty($to)) {
            return false;
        }
        $from = array_search($from, $this->map);
        $to = array_search($to, $this->map);
        if ($from === false || $to === false) {
            return false;
        }
        $salt = mt_rand(1, 10000);
        $data = [ 
            'q' => $q,
            'from' => $from,
            'to' => $to,
            'appKey' => $this->appKey,
            'salt' => $salt,
            'sign' => md5($this->appKey . $q . $salt . $this->secretKey)
        ];
        $query = http_build_query($data);
        $url = $this->url . '?' . $query;
        $httpCurl = new HttpCurl($url, false, 10);
        $res = $httpCurl->execGet();
        if (empty($res)) {
            return false;
        }
        $res = json_decode($res, true);
        if (isset($res['translation'][0]) && !empty($res['translation'][0])) {
            return $res['translation'][0];
        }
        return false;
    }
}