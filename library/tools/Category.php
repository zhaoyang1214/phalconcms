<?php
/** 
 * @desc 分类格式化 
 * @author ZhaoYang 
 * @date 2018年9月17日 下午5:11:15 
 * <pre>
 *      $toolsCategory = new \Library\Tools\Category($list, ['title' => 'name', 'fulltitle' => 'cname']);
        $list = $toolsCategory->reclassify();
 * </pre>
 */
namespace Library\Tools;

class Category {
    
    private $categoryList;
    
    private $field;
    
    private $pad;
    
    private $icon;
    
    // 最大深度
    private $maxDepth = null;
    
    public function __construct(array $categoryList, array $field = [ ], string $pad = '&nbsp;&nbsp;', array $icon = null) {
        $this->categoryList = $categoryList;
        $this->field['id'] = $field['id'] ?? 'id';
        $this->field['pid'] = $field['pid'] ?? 'pid';
        $this->field['title'] = $field['title'] ?? 'title';
        // 格式化后名称
        $this->field['fulltitle'] = $field['fulltitle'] ?? 'fulltitle';
        // 子栏目集合名
        $this->field['child'] = $field['child'] ?? 'child';
        $this->pad = $pad;
        $this->icon = $icon ?? ['│', '├', '└'];
    }
    
    public function setMaxDepth($maxDepth) {
        $this->maxDepth = $maxDepth;
        return $this;
    }
    
    public function reclassify($pid = 0, $pad = '', $depth=1) {
        static $newCategory = [ ];
        $childCategory = $this->getChildCategory($pid);
        if(is_null($this->maxDepth) || $this->maxDepth > $depth) {
            foreach ($childCategory as $k => $v) {
                $padding = '';
                if($k + 1 == count($childCategory)) {
                    $pre = $this->icon[2];
                } else {
                    $pre =  $this->icon[1];
                    $padding = $pad ? $this->icon[0] : '';
                }
                
                $v[$this->field['fulltitle']] = ($pad ? $pad . $pre : '') . $v[$this->field['title']];
                $newCategory[] = $v;
                $this->reclassify($v[$this->field['id']], $pad . $padding . $this->pad, $depth + 1);
            }
        }
        return $newCategory;
    }
    
    private function getChildCategory($id) {
        $childCategory = [ ];
        foreach ($this->categoryList as $k => $v) {
            if($id == $v[$this->field['pid']]) {
                $childCategory[] = $v;
                unset($this->categoryList[$k]);
            }
        }
        return $childCategory;
    }
    
    /**
     * @desc 栏目归类
     * @param mixed $id 父类id
     * @param int $depth 当前节点深度
     * @author: ZhaoYang
     * @date: 2018年10月1日 下午10:19:55
     */
    public function categoryGroup($id, $depth=1) {
        $category = $this->getChildCategory($id);
        if(is_null($this->maxDepth) || $this->maxDepth > $depth) {
            foreach ($category as $k => $v) {
                $childCategory = $this->categoryGroup($v[$this->field['id']], $depth + 1);
                $category[$k][$this->field['child']] = $childCategory;
            }
        }
        return $category;
    }
    
    public function getCategoryById($id) {
        $category = [ ];
        foreach ($this->categoryList as $k => $v) {
            if($id == $v[$this->field['id']]) {
                $category = $v;
                unset($this->categoryList[$k]);
                break;
            }
        }
        return $category;
    }
    
    public function getParents($id, $depth=1) {
        $category = [];
        $nowCategory = $this->getCategoryById($id);
        if(empty($nowCategory)) {
            return $category;
        }
        $category[] = $nowCategory;
        if(is_null($this->maxDepth) || $this->maxDepth > $depth) {
            $parents = $this->getParents($nowCategory[$this->field['pid']], $depth);
            $category = array_merge($category, $parents);
        }
        return $category;  
    }
    
    public function getSons($id, $depth=1) {
        $category = [];
        $childCategory = $this->getChildCategory($id);
        if(empty($childCategory)) {
            return $category;
        }
        if(is_null($this->maxDepth) || $this->maxDepth >= $depth) {
            foreach ($childCategory as $k => $v) {
                $category[] = $v;
                $sons = $this->getSons($v[$this->field['id']], $depth + 1);
                $category = array_merge($category, $sons);
            }
        }
        return $category;
    }
}