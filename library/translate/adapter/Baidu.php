<?php
/**
 * @desc 百度翻译
 * @author: ZhaoYang
 * @date: 2018年7月23日 下午10:37:17
 */
namespace Library\Translate\Adapter;

use Library\Tools\HttpCurl;
use Library\Translate\AdapterInterface;

class Baidu implements AdapterInterface {

    private $url;

    private $appid;

    private $secretKey;

    private $map;

    public function __construct(array $config = null) {
        if (!is_null($config)) {
            if (!isset($config['url']) || empty($config['url'])) {
                throw new \Exception('url 不能为空');
            }
            if (!isset($config['appid']) || empty($config['appid'])) {
                throw new \Exception('appid 不能为空');
            }
            if (!isset($config['secretKey']) || empty($config['secretKey'])) {
                throw new \Exception('secretKey 不能为空');
            }
            if (!isset($config['map']) || !is_array($config['map'])) {
                throw new \Exception('map 不能为空且必须为数组');
            }
            $this->url = $config['url'];
            $this->appid = $config['appid'];
            $this->secretKey = $config['secretKey'];
            $this->map = $config['map'];
        }
    }

    /** 
     * @desc 需要配置的参数 
     * @return array
     * @author ZhaoYang 
     * @date 2018年7月25日 上午10:40:07 
     */
    public static function needSetConfig(): array {
        return [ 
            'url' => '翻译API请求地址',
            'appid' => 'APP ID',
            'secretKey' => '密钥'
        ];
    }

    /** 
     * @desc 支持的基础语言 
     * @return array
     * @author ZhaoYang 
     * @date 2018年7月25日 上午10:40:20 
     */
    public static function baseLanguage(): array {
        return [ 
            'zh' => '中文',
            'en' => '英语',
            'yue' => '粤语',
            'wyw' => '文言文',
            'jp' => '日语',
            'kor' => '韩语',
            'fra' => '法语',
            'spa' => '西班牙语',
            'th' => '泰语',
            'ara' => '阿拉伯语',
            'ru' => '俄语',
            'pt' => '葡萄牙语',
            'de' => '德语',
            'it' => '意大利语',
            'el' => '希腊语',
            'nl' => '荷兰语',
            'pl' => '波兰语',
            'bul' => '保加利亚语',
            'est' => '爱沙尼亚语',
            'dan' => '丹麦语',
            'fin' => '芬兰语',
            'cs' => '捷克语',
            'rom' => '罗马尼亚语',
            'slo' => '斯洛文尼亚语',
            'swe' => '瑞典语',
            'hu' => '匈牙利语',
            'cht' => '繁体中文',
            'vie' => '越南语'
        ];
    }

    /** 
     * @desc 发送请求翻译
     * @param string $q 待翻译字符串
     * @param string $from 翻译源语言
     * @param string $to 译文语言
     * @return: string|bool
     * @author ZhaoYang 
     * @date 2018年7月25日 上午10:41:17 
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
            'appid' => $this->appid,
            'salt' => $salt,
            'sign' => md5($this->appid . $q . $salt . $this->secretKey)
        ];
        $query = http_build_query($data);
        $url = $this->url . '?' . $query;
        $httpCurl = new HttpCurl($url, false, 10);
        $res = $httpCurl->execGet();
        if (empty($res)) {
            return false;
        }
        $res = json_decode($res, true);
        if (isset($res['trans_result'][0]['dst']) && !empty($res['trans_result'][0]['dst'])) {
            return $res['trans_result'][0]['dst'];
        }
        return false;
    }
}