<?php
namespace App\Admin\Models;

use Models\Category as ModelsCategory;
use Common\Common;
use Common\Validate;
use Library\Vendors\Pinyin\Pinyin;

class Category extends ModelsCategory
{

    /**
     * 规则
     *
     * @author ZhaoYang
     *         @date 2018年9月11日 上午10:04:38
     */
    public function rules()
    {
        return [
            'id0' => [
                'id',
                'callback',
                '非法操作',
                function ($data) {
                    if (! isset($data['id'])) {
                        return false;
                    }
                    $info = self::findFirst(intval($data['id']));
                    $session = $this->getDI()->getSession();
                    $adminGroupInfo = $session->get('adminGroupInfo');
                    if ($info === false || ($adminGroupInfo['language_power'] == 1 && $adminGroupInfo['language_id'] != $info->language_id)) {
                        return false;
                    }
                    return true;
                }
            ],
            'pid0' => [
                'pid',
                'callback',
                '上级栏目选择错误',
                function ($data) {
                    if (! isset($data['pid'])) {
                        return false;
                    }
                    if ($data['pid'] == 0) {
                        return true;
                    }
                    $info = self::findFirst(intval($data['pid']));
                    $session = $this->getDI()->getSession();
                    $adminGroupInfo = $session->get('adminGroupInfo');
                    if ($info === false || ($adminGroupInfo['language_power'] == 1 && $adminGroupInfo['language_id'] != $info->language_id)) {
                        return false;
                    }
                    return true;
                }
            ],
            'category_model_id0' => [
                'category_model_id',
                'callback',
                '模型选择错误',
                function ($data) {
                    if (! isset($data['category_model_id'])) {
                        return false;
                    }
                    $info = CategoryModel::findFirst(intval($data['category_model_id']));
                    return $info === false ? false : true;
                }
            ],
            'sequence0' => [
                'sequence',
                'digit',
                '排序必须为整数'
            ],
            'is_show0' => [
                'is_show',
                'inclusionin',
                '是否显示选择错误',
                [
                    0,
                    1
                ]
            ],
            'type0' => [
                'type',
                'inclusionin',
                '栏目类型选择错误',
                [
                    1,
                    2
                ]
            ],
            'name0' => [
                'name',
                'stringlength',
                '栏目名称长度必须大于1位|模型名称必须小于50位',
                [
                    1,
                    50
                ]
            ],
            'urlname0' => [
                'urlname',
                'callback',
                '栏目URL名称已存在',
                function ($data) {
                    if (! isset($data['urlname'])) {
                        return false;
                    }
                    $parameters = [
                        'columns' => 'urlname',
                        'conditions' => 'urlname=:urlname:',
                        'bind' => [
                            'urlname' => $data['urlname']
                        ]
                    ];
                    if (isset($data['id']) && ! empty($data['id'])) {
                        $parameters['conditions'] .= ' AND id<>:id:';
                        $parameters['bind']['id'] = $data['id'];
                    }
                    return self::findFirst($parameters) ? false : true;
                }
            ],
            'page0' => [
                'page',
                'digit',
                '分页必须为整数'
            ],
            'content_order0' => [
                'content_order',
                'callback',
                '内容排序选择错误',
                function ($data) {
                    if (! isset($data['content_order'])) {
                        return false;
                    }
                    return array_key_exists($data['content_order'], self::getContentOrder()) ? true : false;
                }
            ],
            'expand_id0' => [
                'expand_id',
                'callback',
                '扩展模型选择错误',
                function ($data) {
                    if (! isset($data['expand_id'])) {
                        return false;
                    }
                    if ($data['expand_id'] == 0) {
                        return true;
                    }
                    $info = Expand::findFirst(intval($data['expand_id']));
                    return $info === false ? false : true;
                }
            ]
        ];
    }

    public function getAllowCount()
    {
        $session = $this->getDI()->getSession();
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [];
        if (! ($adminGroupInfo['keep'] & 2)) {
            if (empty($adminGroupInfo['category_ids'])) {
                return 0;
            }
            $where[] = 'id IN(' . $adminGroupInfo['category_ids'] . ')';
        }
        if ($adminGroupInfo['language_power'] & 1) {
            $where[] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        $parameters = [];
        if (! empty($where)) {
            $parameters['conditions'] = implode(' AND ', $where);
        }
        return self::count($parameters);
    }

    /**
     * 获取可访问的所有栏目
     *
     * @return array
     * @author : ZhaoYang
     *         @date: 2018年7月29日 上午3:03:37
     */
    public function getAllowList()
    {
        $session = $this->getDI()->getSession();
        $adminInfo = $session->get('adminInfo');
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [];
        if (! ($adminGroupInfo['keep'] & 2)) {
            if (empty($adminGroupInfo['category_ids'])) {
                return [];
            }
            $where[] = 'id IN(' . $adminGroupInfo['category_ids'] . ')';
        }
        if ($adminGroupInfo['language_power'] & 1) {
            $where[] = 'language_id=' . $adminGroupInfo['language_id'];
        }
        $parameters = [
            'order' => 'sequence ASC'
        ];
        if (! empty($where)) {
            $parameters['conditions'] = implode(' AND ', $where);
        }
        $list = self::find($parameters)->toArray();
        if (empty($list)) {
            return [];
        }
        $toolsCategory = new \Library\Tools\Category($list, [
            'title' => 'name',
            'fulltitle' => 'cname'
        ]);
        $list = $toolsCategory->reclassify();
        return $list;
    }

    /**
     * 与category_model表联表查询
     *
     * @return array
     * @author : ZhaoYang
     *         @date: 2018年9月18日 下午11:37:07
     */
    public function getAllowList2()
    {
        $session = $this->getDI()->getSession();
        $adminInfo = $session->get('adminInfo');
        $adminGroupInfo = $session->get('adminGroupInfo');
        $where = [];
        if (! ($adminGroupInfo['keep'] & 2)) {
            if (empty($adminGroupInfo['category_ids'])) {
                return [];
            }
            $where[] = 'App\Admin\Models\Category.id IN(' . $adminGroupInfo['category_ids'] . ')';
        }
        if ($adminGroupInfo['language_power'] & 1) {
            $where[] = 'App\Admin\Models\Category.language_id=' . $adminGroupInfo['language_id'];
        }
        if (! empty($where)) {
            $conditions = implode(' AND ', $where);
        }
        $result = self::query()->columns('App\Admin\Models\Category.id,App\Admin\Models\Category.pid,App\Admin\Models\Category.category_model_id,App\Admin\Models\Category.type,App\Admin\Models\Category.name,b.category,b.content')
            ->leftJoin('App\Admin\Models\CategoryModel', 'category_model_id=b.id', 'b')
            ->where($conditions)
            ->orderBy('App\Admin\Models\Category.sequence ASC,App\Admin\Models\Category.id ASC')
            ->execute()
            ->toArray();
        return $result;
    }

    public function getContentOrder(string $contentOrder = null)
    {
        $map = [
            'updatetime DESC' => '内容更新时间 新->旧',
            'updatetime ASC' => '内容更新时间 旧->新',
            'inputtime DESC' => '内容发布时间 新->旧',
            'inputtime ASC' => '内容发布时间 旧->新',
            'sequence DESC' => '内容自定义排序 大->小',
            'sequence ASC' => '内容自定义排序 小->大'
        ];
        if (is_null($contentOrder)) {
            return $map;
        }
        return $map[$contentOrder] ?? '未知';
    }

    /**
     * 批量插入
     *
     * @author : ZhaoYang
     *         @date: 2018年9月15日 下午4:43:11
     */
    public function addAll(array $data)
    {
        $insertData = Common::arraySlice([
            'pid',
            'category_model_id',
            'sequence',
            'is_show',
            'type',
            'subname',
            'image',
            'category_tpl',
            'content_tpl',
            'page',
            'keywords',
            'description',
            'seo_content',
            'content_order',
            'expand_id'
        ], $data);
        $validate = new Validate();
        $message = $validate->addRules(self::getRules([
            'pid0',
            'category_model_id0',
            'sequence0',
            'is_show0',
            'type0',
            'page0',
            'content_order0',
            'expand_id0'
        ], false))->validate($insertData);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        if (isset($insertData['keywords'])) {
            $insertData['keywords'] = str_replace('，', ',', $insertData['keywords']);
        }
        if (isset($insertData['seo_content'])) {
            $insertData['seo_content'] = htmlspecialchars(Common::HTMLPurifierMeta($insertData['seo_content']));
        }
        $adminGroupInfo = $this->getDI()
            ->getSession()
            ->get('adminGroupInfo');
        $insertData['language_id'] = $adminGroupInfo['language_id'];
        if (! isset($data['namelist']) || empty($data['namelist'])) {
            return $this->errorMessage('栏目名称不能为空');
        }
        $namelist = explode("\n", $data['namelist']);
        $pinyin = new Pinyin();
        $connection = $this->getWriteConnection();
        $connection->begin();
        $rules = self::getRules([
            'name0',
            'urlname0'
        ]);
        try {
            foreach ($namelist as $v) {
                $nameArr = explode('|', $v);
                $name = $nameArr[0];
                $urlname = $nameArr[1] ?? $pinyin->permalink($name, '');
                if (strlen($urlname) > 100) {
                    $urlname = substr($urlname, 0, 68) . md5(substr($urlname, 68));
                }
                $insertData['name'] = $name;
                $insertData['urlname'] = $urlname;
                $message = $validate->addRules($rules)->validate($insertData);
                if (count($message)) {
                    $connection->rollback();
                    return $this->errorMessage($message);
                }
                $result = $this->create($insertData);
                if ($result === false) {
                    $connection->rollback();
                    return false;
                }
                $this->reset();
                unset($this->id);
            }
            return $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            return $this->errorMessage($e->getMessage());
        }
    }

    /**
     * 单个插入
     *
     * @author : ZhaoYang
     *         @date: 2018年9月15日 下午4:43:23
     */
    public function add(array $data)
    {
        $insertData = Common::arraySlice([
            'pid',
            'category_model_id',
            'name',
            'urlname',
            'sequence',
            'is_show',
            'type',
            'subname',
            'image',
            'category_tpl',
            'content_tpl',
            'page',
            'keywords',
            'description',
            'seo_content',
            'content_order',
            'expand_id'
        ], $data);
        if (empty($insertData['urlname'])) {
            $pinyin = new Pinyin();
            $urlname = $pinyin->permalink($insertData['name'], '');
            if (strlen($urlname) > 100) {
                $urlname = substr($urlname, 0, 68) . md5(substr($urlname, 68));
            }
            $insertData['urlname'] = $urlname;
        }
        $validate = new Validate();
        $message = $validate->addRules(self::getRules([
            'pid0',
            'name0',
            'urlname0',
            'category_model_id0',
            'sequence0',
            'is_show0',
            'type0',
            'page0',
            'content_order0',
            'expand_id0'
        ], false))->validate($insertData);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        if (isset($insertData['keywords'])) {
            $insertData['keywords'] = str_replace('，', ',', $insertData['keywords']);
        }
        if (isset($insertData['seo_content'])) {
            $insertData['seo_content'] = htmlspecialchars(Common::HTMLPurifierMeta($insertData['seo_content']));
        }
        $adminGroupInfo = $this->getDI()
            ->getSession()
            ->get('adminGroupInfo');
        $insertData['language_id'] = $adminGroupInfo['language_id'];
        return $this->create($insertData);
    }

    public function getInfoById(int $id)
    {
        $message = (new Validate())->addRules(self::getRules([
            'id0'
        ]))->validate([
            'id' => $id
        ]);
        if (count($message)) {
            return $this->errorMessage('非法操作！');
        }
        return self::findFirst($id);
    }

    public function updateSequenceById(int $id, int $sequence = 0)
    {
        $data = [
            'id' => $id,
            'sequence' => $sequence
        ];
        $message = (new Validate())->addRules(self::getRules([
            'id0',
            'sequence0'
        ]))->validate($data);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($id);
        $this->assign($info->toArray());
        return $this->update($data);
    }

    public function edit(array $data)
    {
        $updateData = Common::arraySlice([
            'id',
            'pid',
            'name',
            'urlname',
            'sequence',
            'is_show',
            'type',
            'subname',
            'image',
            'category_tpl',
            'content_tpl',
            'page',
            'keywords',
            'description',
            'seo_content',
            'content_order',
            'expand_id'
        ], $data);
        if (empty($updateData['urlname'])) {
            $pinyin = new Pinyin();
            $urlname = $pinyin->permalink($updateData['name'], '');
            if (strlen($urlname) > 100) {
                $urlname = substr($urlname, 0, 68) . md5(substr($urlname, 68));
            }
            $updateData['urlname'] = $urlname;
        }
        $validate = new Validate();
        $message = $validate->addRules(self::getRules([
            'id0',
            'pid0',
            'name0',
            'urlname0',
            'sequence0',
            'is_show0',
            'type0',
            'page0',
            'content_order0',
            'expand_id0'
        ], false))->validate($updateData);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        if (isset($updateData['keywords'])) {
            $updateData['keywords'] = str_replace('，', ',', $updateData['keywords']);
        }
        if (isset($updateData['seo_content'])) {
            $updateData['seo_content'] = htmlspecialchars(Common::HTMLPurifierMeta($updateData['seo_content']));
        }
        $info = self::findFirst($updateData['id']);
        $this->assign($info->toArray());
        return $this->update($updateData);
    }

    public function del(int $id)
    {
        $message = (new Validate())->addRules(self::getRules([
            'id0'
        ]))->validate([
            'id' => $id
        ]);
        if (count($message)) {
            return $this->errorMessage($message);
        }
        $info = self::findFirst($id);
        return $info->delete();
    }
}