<?php
/**
 * @desc 分页类
 * @author: ZhaoYang
 * @date: 2018年7月20日 下午10:07:11
 * <pre>
 * use Library\Tools\Paginator;
 * 方法1：
 * $paginator1 = new Paginator(100);
   echo $paginator1->show();
        方法2：
   $config2 = [
        'page' => 'p',
        'pagePattern' => '/([\/&?]p[\/=])([\d]+)/',
        'prevTheme' => '<a {href}><li class="{class}">上一页</li></a>',
        'nextTheme' => '<a {href}><li class="{class}">下一页</li></a>',
        'suffixTheme' => '<li class="totalPage">共<span>{totalPages}</span>页  当前第<span>{nowPage}</span>页</li>',
        'showTheme' => '<ul class="pageMenu clearfix">{getSuffixPage}{getFirstLinkPage}{getPrevLinkPage}{getLeftLinkPage}{getNowLinkPage}{getRightLinkPage}{getNextLinkPage}{getLastLinkPage}</ul>'
    ];
    $paginator2 = new Paginator(100, 7, null, $config2);
    echo $paginator2->show();
        方法3：
     $config3 = [
         'page' => 'pa',
         'pagePattern' => '/([\/&?]pa[\/=])([\d]+)/',
         'suffixTheme' => '<li class="totalPage">共<span>{totalPages}</span>页  当前第<span>{nowPage}</span>页</li>',
         'showTheme' => '{getFirstLinkPage}{getPrevLinkPage}{getLeftLinkPage}{getNowLinkPage}{getRightLinkPage}{getNextLinkPage}{getLastLinkPage}{getSuffixPage}',
         'isShowDisabled' => false
     ];
     $paginator3 = new Paginator(100, 5, null, $config3);
     echo $paginator3->show();   
     echo $paginator3->getLimit() . '<br>';
	 echo $paginator3->getLimit(true) . '<br>';
	 echo $paginator3->getOffset() . '<br>';
 * </pre>
 */

namespace Library\Tools;

class Paginator {

    // 分页参数名
    protected $page = 'page';

    // url中分页正则
    protected $pagePattern = '/([\/&?]page[\/=])([\d]+)/';

    // url
    protected $url = null;

    // url模板
    protected $urlTheme = null;

    // href
    protected $href = null;

    // href模板
    protected $hrefTheme = 'href="{urlTheme}"';

    // 禁用页class值
    protected $disabled = 'disabled';

    // 选中页class值
    protected $active = 'active';

    // 第一页模板
    protected $firstTheme = '<a {href}><li class="{class}">首页</li></a>';

    // 上一页模板
    protected $prevTheme = '<a {href}><li class="{class}"><</li></a>';

    // 当前页模板
    protected $nowTheme = '<a><li class="{class}">{nowPage}</li></a>';

    // 其它页模板
    protected $otherTheme = '<a {href}><li class="{class}">{otherPage}</li></a>';

    // 下一页模板
    protected $nextTheme = '<a {href}><li class="{class}">></li></a>';

    // 最后一页模板
    protected $lastTheme = '<a {href}><li class="{class}">尾页</li></a>';

    // 后缀模板
    protected $suffixTheme = '<li class="totalPage">共<span>{totalPages}</span>页 </li>';

    // protected $suffixTheme = '<li class="totalPage">共<span>{totalPages}</span>页 当前<span>{nowPage}</span>页 </li>';
    
    // 当前页左边显示$otherTheme的个数
    protected $leftSideAmount = 3;

    // 当前页右边显示$otherTheme的个数
    protected $rightSideAmount = 3;

    // 总记录数
    protected $totalRows = 0;

    // 每页显示的行数
    protected $listRows = 10;

    // 总页数
    protected $totalPages = 0;

    // 起始页码
    protected $startPage = 1;

    // 当前页码
    protected $nowPage = 1;

    // 展示的模板
    protected $showTheme = '{getFirstLinkPage}{getPrevLinkPage}{getLeftLinkPage}{getNowLinkPage}{getRightLinkPage}{getNextLinkPage}{getLastLinkPage}{getSuffixPage}';

    // 生成各部分时替换，当存在该属性时则替换成属性值，若该属性不存在，但存在该方法，则替换成该方法返回值，否则替换为当前设置的值
    protected $replaceRule = [ 
        // '{url}' => 'url', // 动态变化
        '{urlTheme}' => 'urlTheme',
        '{href}' => 'href', // 动态变化
        '{class}' => 'class', // 动态变化
        '{nowPage}' => 'nowPage',
        '{otherPage}' => 'otherPage', // 动态变化
        '{totalPages}' => 'totalPages',
        '{getFirstLinkPage}' => 'getFirstLinkPage',
        '{getPrevLinkPage}' => 'getPrevLinkPage',
        '{getLeftLinkPage}' => 'getLeftLinkPage',
        '{getNowLinkPage}' => 'getNowLinkPage',
        '{getRightLinkPage}' => 'getRightLinkPage',
        '{getNextLinkPage}' => 'getNextLinkPage',
        '{getLastLinkPage}' => 'getLastLinkPage',
        '{getSuffixPage}' => 'getSuffixPage'
    ];

    // 是否显示无效按钮（首页、上一页、下一页、尾页）
    protected $isShowDisabled = true;
    
   // 定义待替换的页的字符串，如果与url中字符串存在冲突则替换
   protected $pendingReplacePage = '[pendingReplacePage]';

    public function __construct(int $totalRows, int $listRows = 10, string $url = null, array $config = []) {
        $this->totalRows = $totalRows;
        $this->listRows = $listRows;
        $this->url = $url;
        foreach ($config as $attribute => $value) {
            if (method_exists($this, $attribute)) {
                throw new \Exception($attribute . ' 是类方法！');
            }
            $this->{$attribute} = $value;
        }
        $this->totalPages = ceil($this->totalRows / $this->listRows);
        $this->initUrlTheme();
        $this->initHrefTheme();
    }

    public function __get(string $name) {
        return $this->{$name} ?? null;
    }
    
    protected function initUrlTheme() {
        $this->url = $this->url ?? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->checkUrl();
        if (!empty($this->pagePattern) && preg_match($this->pagePattern, $this->url, $matches) && isset($matches[1]) && isset($matches[2])) {
            if (intval($matches[2])) {
                $this->nowPage = $matches[2] > $this->totalPages ? $this->totalPages : $matches[2];
            }
            $this->urlTheme = str_replace($matches[0], $matches[1] . $this->pendingReplacePage, $this->url);
        } else {
            $this->urlTheme = $this->url . (strpos($this->url, '?') ? '&' : '?') . $this->page . '=' . $this->pendingReplacePage;
        }
        $this->urlTheme = urldecode($this->urlTheme);
    }

    protected function initHrefTheme() {
        $this->hrefTheme = $this->replace($this->hrefTheme);
    }
    
    private function checkUrl() {
        check_url:
        if(strpos($this->url, $this->pendingReplacePage) !== false){
            $this->pendingReplacePage = '[' . mt_rand(1, 1000000) . ']';
            goto check_url;
        }
    }
    
     /**
      * @desc 设置替换规则
      * @param array $replaceRule 替换规则
      * @param  bool $cover 是否覆盖现有规则
      * @return: null
      * @author: ZhaoYang
      * @date: 2018年7月21日 下午11:11:44
      */
    public function setReplaceRule(array $replaceRule, bool $cover = false) {
        $this->replaceRule = $cover ? $replaceRule : array_merge($this->replaceRule, $replaceRule);
    }

     /**
      * @desc 模板内容替换
      * @param string $subject 待替换的字符串
      * @param array $replaceRule 替换规则
      * @return: string
      * @author: ZhaoYang
      * @date: 2018年7月21日 下午11:05:57
      */
    private function replace(string $subject, array $replaceRule = null) {
        $replaceRule = $replaceRule ?? $this->replaceRule;
        foreach ($replaceRule as $search => $replace) {
            if (strpos($subject, $search) !== false) {
                if (isset($this->$replace)) {
                    $replace = $this->{$replace};
                } else if (method_exists($this, $replace)) {
                    $replace = $this->{$replace}();
                }
                $subject = str_replace($search, $replace, $subject);
            }
        }
        return $subject;
    }

    /**
     * @desc 获取首页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:13:47
     */
    public function getFirstLinkPage() {
        $this->otherPage = $this->startPage;
        if ($this->nowPage <= $this->startPage) {
            $this->href = '';
            $this->class = $this->disabled;
            $this->url = '';
            if (!$this->isShowDisabled) {
                return '';
            }
        } else {
            $this->href = str_replace($this->pendingReplacePage, $this->startPage, $this->hrefTheme);
            $this->class = '';
            $this->url = str_replace($this->pendingReplacePage, $this->startPage, $this->urlTheme);
        }
        return $this->replace($this->firstTheme);
    }

    /**
     * @desc 获取上一页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:14:09
     */
    public function getPrevLinkPage() {
        if ($this->nowPage <= $this->startPage) {
            $this->otherPage = '';
            $this->href = '';
            $this->class = $this->disabled;
            $this->url = '';
            if (!$this->isShowDisabled) {
                return '';
            }
        } else {
            $this->otherPage = $this->nowPage - 1;
            $this->href = str_replace($this->pendingReplacePage, $this->otherPage, $this->hrefTheme);
            $this->class = '';
            $this->url = str_replace($this->pendingReplacePage, $this->otherPage, $this->hrefTheme);
        }
        return $this->replace($this->prevTheme);
    }

    /**
     * @desc 获取当前页左边页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:14:21
     */
    public function getLeftLinkPage() {
        $leftLinkPage = '';
        $leftSideAmount = $this->leftSideAmount;
        $rightSideAmount = $this->rightSideAmount;
        $subPage = $this->totalPages - $this->nowPage;
        if ($subPage < $rightSideAmount) {
            $leftSideAmount += $rightSideAmount - $subPage;
        }
        for ($i = 1; $i <= $leftSideAmount; $i ++) {
            $leftPage = $this->nowPage - $i;
            if ($leftPage < $this->startPage) {
                break;
            }
            $this->href = str_replace($this->pendingReplacePage, $leftPage, $this->hrefTheme);
            $this->class = '';
            $this->otherPage = $leftPage;
            $this->url = str_replace($this->pendingReplacePage, $leftPage, $this->urlTheme);
            $leftLinkPage = $this->replace($this->otherTheme) . $leftLinkPage;
        }
        return $leftLinkPage;
    }

    /**
     * @desc 获取当前页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:14:40
     */
    public function getNowLinkPage() {
        $this->otherPage = $this->nowPage;
        $this->href = str_replace($this->pendingReplacePage, $this->nowPage, $this->hrefTheme);
        $this->class = $this->active;
        $this->url = str_replace($this->pendingReplacePage, $this->nowPage, $this->urlTheme);
        return $this->replace($this->nowTheme);
    }

    /**
     * @desc 获取当前页右边页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:14:51
     */
    public function getRightLinkPage() {
        $rightLinkPage = '';
        $rightSideAmount = $this->rightSideAmount;
        $leftSideAmount = $this->leftSideAmount;
        $subPage = $this->nowPage - $this->startPage;
        if ($subPage < $leftSideAmount) {
            $rightSideAmount += $leftSideAmount - $subPage;
        }
        for ($i = 1; $i <= $rightSideAmount; $i ++) {
            $rightPage = $this->nowPage + $i;
            if ($rightPage > $this->totalPages) {
                break;
            }
            $this->href = str_replace($this->pendingReplacePage, $rightPage, $this->hrefTheme);
            $this->class = '';
            $this->otherPage = $rightPage;
            $this->url = str_replace($this->pendingReplacePage, $rightPage, $this->urlTheme);
            $rightLinkPage .= $this->replace($this->otherTheme);
        }
        return $rightLinkPage;
    }

    /**
     * @desc 获取下一页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:15:04
     */
    public function getNextLinkPage() {
        if ($this->nowPage >= $this->totalPages) {
            $this->otherPage = '';
            $this->href = '';
            $this->class = $this->disabled;
            $this->url = '';
            if (!$this->isShowDisabled) {
                return '';
            }
        } else {
            $this->otherPage = $this->nowPage + 1;
            $this->href = str_replace($this->pendingReplacePage, $this->otherPage, $this->hrefTheme);
            $this->class = '';
            $this->url = str_replace($this->pendingReplacePage, $this->otherPage, $this->urlTheme);
        }
        return $this->replace($this->nextTheme);
    }

    /**
     * @desc 获取最后一页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:15:15
     */
    public function getLastLinkPage() {
        $this->otherPage = $this->totalPages;
        if ($this->nowPage >= $this->totalPages) {
            $this->href = '';
            $this->class = $this->disabled;
            $this->url = '';
            if (!$this->isShowDisabled) {
                return '';
            }
        } else {
            $this->href = str_replace($this->pendingReplacePage, $this->totalPages, $this->hrefTheme);
            $this->class = '';
            $this->url = str_replace($this->pendingReplacePage, $this->totalPages, $this->urlTheme);
        }
        return $this->replace($this->lastTheme);
    }

    /**
     * @desc 获取后缀（or 前缀）
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:15:25
     */
    public function getSuffixPage() {
        return $this->replace($this->suffixTheme);
    }

    /**
     * @desc 合成页
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:15:45
     */
    public function show() {
        return $this->totalPages < 2 ? '' : $this->replace($this->showTheme, $this->replaceRule);
    }

    /**
     * @desc 获取limit
     * @param bool $offset 是否返回offset
     * @return int|string
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:16:25
     */
    public function getLimit(bool $offset = false) {
        return $offset ? ($this->nowPage - 1) * $this->listRows . ',' . $this->listRows : $this->listRows;
    }

    /**
     * @desc 获取offset
     * @return int
     * @author: ZhaoYang
     * @date: 2018年7月21日 下午11:16:56
     */
    public function getOffset() {
        return ($this->nowPage - 1) * $this->listRows;
    }
    
    public function getPrevHref() {
        return $this->nowPage <= $this->startPage ? '' : str_replace($this->pendingReplacePage, $this->nowPage - 1, $this->urlTheme);
    }
    
    public function getNextHref() {
        return $this->nowPage >= $this->totalPages ? '' : str_replace($this->pendingReplacePage, $this->nowPage + 1, $this->urlTheme);
    }
}