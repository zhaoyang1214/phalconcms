<?php
namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use App\Admin\Models\AdminAuth;
use App\Admin\Models\Translate;
use App\Admin\Models\Category;
use Library\Tools\Paginator;
use App\Admin\Models\Position;
use App\Admin\Models\CategoryContent;
use App\Admin\Models\ExpandField;
use App\Admin\Models\CategoryModel;
use App\Admin\Models\CategoryContentData;
use App\Admin\Models\Expand;
use App\Admin\Models\ExpandData;
use App\Admin\Models\CategoryContentPosition;
use App\Admin\Models\Tags;
use App\Admin\Models\TagsRelation;

class CategorycontentController extends CommonController {

    public function manageAction() {
        $adminAuthInfo = AdminAuth::getInfoByConAct($this->dispatcher->getControllerName(), $this->dispatcher->getActionName());
        $authList = [ ];
        if ($adminAuthInfo !== false) {
            $authList = (new AdminAuth())->getAllowList($adminAuthInfo->id);
            $this->view->authName = (new Translate())->t($adminAuthInfo->name);
        }
        $category = new Category();
        $categoryList = $category->getAllowList2();
        foreach($categoryList as &$v) {
            if(empty($v['content'])) {
                $v['url'] = $this->url->get($v['category'] . '/info/id/' . $v['id']);
                $v['target'] = 'main';
                $v['icon'] = $this->url->getStatic('ztree/css/img/ico2.gif');
            } else {
                if($v['type'] == 2) {
                    $v['url'] = $this->url->get($v['content'] . '/index/category_id/' . $v['id']);
                    $v['target'] = 'main';
                    $v['icon'] = $this->url->getStatic('ztree/css/img/ico3.gif');
                } else {
                    $v['icon'] = $this->url->getStatic('ztree/css/img/ico1.gif');
                }
            }
        }
        $this->view->authList = $authList;
        $this->view->list = json_encode($categoryList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    public function listAction() {
        $position = $this->get('position', 'int!', 0);
        $search = $this->get('search');
        $where = [];
        $joinPosition = false;
        if(!empty($position)) {
            $where[] = 'd.position_id=' . $position;
            $joinPosition = true;
        }
        if(!empty($search)) {
            $where[] = 'a.title LIKE "%' . $search . '%"';
        }
        if(empty($where)) {
            $where[] = 'a.status=0';
        }
        $conditions = implode(' AND ', $where);
        $categoryContent = new CategoryContent();
        $count = $categoryContent->getAllowCount($conditions, $joinPosition);
        $paginator = new Paginator($count);
        $categoryContentList = $categoryContent->getAllowList($conditions, $paginator->getLimit(true), $joinPosition);
        $category = new Category();
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->position = $position;
        $this->view->search = $search;
        $this->view->count = $count;
        $this->view->categoryContentList = $categoryContentList;
        $where[] = 'a.status=0';
        $this->view->notAuditCount = $categoryContent->getAllowCount(implode(' AND ', $where), $joinPosition);;
        $this->view->categoryCount = $category->getAllowCount();
        $this->view->positionList = (new Position())->getAllowList();
        $this->view->categorycontentInfoPower = $admin->checkPower('categorycontent', 'info');
        $this->view->categorycontentAuditPower = $admin->checkPower('categorycontent', 'audit');
        $this->view->categorycontentQuickEditPower = $admin->checkPower('categorycontent', 'quickEdit');
        $this->view->categorycontentDeletePower = $admin->checkPower('categorycontent', 'delete');
    }
    
    public function indexAction() {
        $categoryId = $this->get('category_id', 'int!', 0);
        $sequence = $this->get('sequence', 'int!', 1);
        $status = $this->get('status', 'int!', 1);
        $position = $this->get('position', 'int!', 0);
        $search = $this->get('search');
        $category = new Category();
        $categoryInfo = $category->getInfoById($categoryId);
        if($categoryInfo === false) {
            return $this->error($category->getMessages()[0]->getMessage());
        }
        $where = [];
        $joinPosition = false;
        if(!empty($categoryId)) {
            $where[] = 'b.id=' . $categoryId;
        }
        switch ($sequence) {
            case 2:
                $order = 'a.updatetime ASC';
                break;
            case 3:
                $order = 'a.id DESC';
                break;
            case 4:
                $order = 'a.id ASC';
                break;
            case 5:
                $order = 'a.inputtime DESC';
                break;
            case 6:
                $order = 'a.inputtime ASC';
                break;
            case 7:
                $order = 'a.views DESC';
                break;
            case 8:
                $order = 'a.views ASC';
                break;
            case 1:
            default:
                $order = 'a.updatetime DESC';
        }
        $where[] = 'a.status=' . $status;
        if(!empty($position)) {
            $where[] = 'd.position_id=' . $position;
            $joinPosition = true;
        }
        if(!empty($search)) {
            $where[] = 'a.title LIKE "%' . $search . '%"';
        }
        if(empty($where)) {
            $where[] = 'a.status=0';
        }
        $conditions = implode(' AND ', $where);
        $categoryContent = new CategoryContent();
        $count = $categoryContent->getAllowCount($conditions, $joinPosition);
        $paginator = new Paginator($count);
        $categoryContentList = $categoryContent->getAllowList($conditions, $paginator->getLimit(true), $joinPosition, $order);
        
        $admin = new Admin();
        $this->view->setTemplateBefore('common');
        $this->view->pageShow = $paginator->show();
        $this->view->sequence = $sequence;
        $this->view->status = $status;
        $this->view->position = $position;
        $this->view->search = $search;
        $this->view->categoryContentList = $categoryContentList;
        $this->view->category = $categoryInfo;
        $this->view->positionList = (new Position())->getAllowList();
        $this->view->categorycontentAddPower = $admin->checkPower('categorycontent', 'add');
        $this->view->categorycontentInfoPower = $admin->checkPower('categorycontent', 'info');
        $this->view->categorycontentAuditPower = $admin->checkPower('categorycontent', 'audit');
        $this->view->categorycontentQuickEditPower = $admin->checkPower('categorycontent', 'quickEdit');
        $this->view->categorycontentDeletePower = $admin->checkPower('categorycontent', 'delete');
        $this->view->categorycontentMovePower = $admin->checkPower('categorycontent', 'move');
        $this->view->categoryList = $category->getAllowList();
    }
    
    public function addAction() {
        if($this->request->isPost()) {
            $data = $this->post();
            $this->db->begin();
            $categoryContent = new CategoryContent();
            $addRes = $categoryContent->add($data);
            if($addRes === false) {
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
            $data['category_content_id'] = $categoryContent->id;
            $data['content'] = $this->post('content', false);
            $categoryContentData = new CategoryContentData();
            $addRes = $categoryContentData->add($data);
            if($addRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContentData->getMessages()[0]->getMessage(), 10001);
            }
            $category = Category::findFirst($categoryContent->category_id);
            if(!empty($category->expand_id)) {
                $expand = Expand::findFirst($category->expand_id);
                if($expand !== false) {
                    $expandData = new ExpandData($expand->table);
                    $addRes = $expandData->addOrEdit($this->post(null, false), $expand->id, $categoryContent->id);
                    if($addRes === false) {
                        $this->db->rollback();
                        return $this->sendJson($expandData->getMessages()[0]->getMessage(), 10001);
                    }
                }
            }
            if(isset($data['position']) && !empty($data['position']) && is_array($data['position'])) {
                $categoryContentPosition = new CategoryContentPosition();
                $addRes = $categoryContentPosition->addAll($data['position'], $categoryContent->id);
                if($addRes === false) {
                    $this->db->rollback();
                    return $this->sendJson($categoryContentPosition->getMessages()[0]->getMessage(), 10001);
                }
            }
            if(!empty($data['keywords'])) {
                $keywords = explode(',', $data['keywords']);
                $adminGroupInfo = $this->session->get('adminGroupInfo');
                foreach ($keywords as $keyword) {
                    $tags = Tags::findFirst('name="' . $keyword . '"');
                    if($tags === false) {
                        $tags = new Tags();
                        $tags->create([
                            'name' => $keyword,
                            'language_id' => $adminGroupInfo['language_id']
                        ]);
                    }
                    (new TagsRelation())->create([
                        'category_content_id' => $categoryContent->id,
                        'tags_id' => $tags->id
                    ]);
                }
            }
            $this->db->commit();
            return $this->sendJson('添加成功！');
        }
        $categoryId = $this->get('category_id', 'int!', 0);
        $category = new Category();
        $categoryInfo = $category->getInfoById($categoryId);
        if($categoryInfo === false) {
            return $this->error($category->getMessages()[0]->getMessage());
        }
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('categorycontent/add');
        $this->view->actionName = $translate->t('添加');
        $this->view->actionPower = $admin->checkPower('categorycontent', 'add');
        $this->view->categorycontentAuditPower = $admin->checkPower('categorycontent', 'audit');
        $this->view->jumpButton = $translate->t('继续添加');
        $this->view->action = 'add';
        $this->view->categoryList = $category->getAllowList();
        $this->view->category = $categoryInfo;
        $this->view->positionList = (new Position())->getAllowList();
        $this->view->expandFieldList = (new ExpandField())->find('expand_id=' . $categoryInfo->expand_id);
        $this->view->expandData = null;
        $this->view->categoryModel = (new CategoryModel())->findFirst($categoryInfo->category_model_id);
        $this->view->setTemplateBefore('common');
        $this->view->pick('categorycontent/info');
    }
    
    /**
     * @desc 获取关键词
     * @author: ZhaoYang
     * @date: 2018年9月23日 上午12:50:05
     */
    public function getKeywordsAction() {
        $text = $this->post('text');
        $limit = $this->post('limit', 'absint', 5);
        $cws = new \PSCWS4();
        $cws->set_charset('utf8');
        $cws->set_dict();
        $cws->set_rule();
        $cws->set_ignore(true);
        $cws->set_duality(true);
        $cws->send_text($text);
        $result = $cws->get_tops($limit,'r,v,p');
        $cws->close();
        return $this->sendJson($result==false ? [] : $result);
    }
    
    /**
     * @desc 快速编辑
     * @author: ZhaoYang
     * @date: 2018年9月24日 下午6:15:29
     */
    public function quickEditAction() {
        if($this->request->isPost()) {
            $data = $this->post();
            $this->db->begin();
            $categoryContent = new CategoryContent();
            $editRes = $categoryContent->quickEdit($data);
            if ($editRes === false) {
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
            $position = isset($data['position']) && is_array($data['position']) ? $data['position'] : [];
            $categoryContentPositionList = CategoryContentPosition::find('category_content_id=' . $categoryContent->id)->toArray();
            $categoryContentPositionPositionIds = array_column($categoryContentPositionList, 'position_id');
            $addData = array_values(array_diff($position, $categoryContentPositionPositionIds));
            $delData = array_values(array_diff($categoryContentPositionPositionIds, $position));
            $categoryContentPosition = new CategoryContentPosition();
            if(!empty($addData)) {
                $addRes = $categoryContentPosition->addAll($addData, $categoryContent->id);
                if($addRes === false) {
                    $this->db->rollback();
                    return $this->sendJson($categoryContentPosition->getMessages()[0]->getMessage(), 10001);
                }
            }
            if(!empty($delData)) {
                $positionIds = implode(',', $delData);
                $delRes = $categoryContentPosition->deleteByPositionIds($positionIds);
                if($delRes === false) {
                    $this->db->rollback();
                    return $this->sendJson('删除推荐位失败', 10001);
                }
            }
            $keywords = empty($data['keywords']) ? [] : explode(',', $data['keywords']);
            $adminGroupInfo = $this->session->get('adminGroupInfo');
            $tagsIds = [];
            foreach ($keywords as $keyword) {
                $tags = Tags::findFirst('name="' . $keyword . '"');
                if($tags === false) {
                    $tags = new Tags();
                    $tags->create([
                        'name' => $keyword,
                        'language_id' => $adminGroupInfo['language_id']
                    ]);
                }
                $tagsIds[] = $tags->id;
            }
            $tagsRelationList = TagsRelation::find('category_content_id=' . $categoryContent->id)->toArray();
            $tagsRelationIds = array_column($tagsRelationList, 'tags_id');
            $addTagsData = array_values(array_diff($tagsIds, $tagsRelationIds));
            $delTagsData = array_values(array_diff($tagsRelationIds, $tagsIds));
            foreach ($addTagsData as $v) {
                (new TagsRelation())->create([
                    'category_content_id' => $categoryContent->id,
                    'tags_id' => $v
                ]);
            }
            if(!empty($delTagsData)) {
                (new TagsRelation())->deleteById(implode(',', $delTagsData));
            }
            $this->db->commit();
            return $this->sendJson('修改成功！');
        }
        $id = $this->get('id', 'absint', 0);
        $categoryContent = new CategoryContent();
        $info = $categoryContent->getInfoById($id);
        if ($info === false) {
            $this->error($categoryContent->getMessages()[0]->getMessage(), false);
        }
        $this->view->setTemplateBefore('common');
        $this->view->categoryContent = $info;
        $this->view->positionList = (new Position())->getAllowList();
        $this->view->categorycontentAuditPower = (new Admin())->checkPower('categorycontent', 'audit');
    }
    
    /**
     * @desc 修改状态
     * @author: ZhaoYang
     * @date: 2018年9月24日 下午6:15:38
     */
    public function auditAction() {
        $status = $this->post('status', 'absint', 0);
        $ids = trim($this->post('id'), ',');
        if(empty($ids)) {
            return $this->sendJson('未选中要修改的记录', 10001);
        }
        $idArr = explode(',', $ids);
        $this->db->begin();
        foreach ($idArr as $id) {
            $categoryContent = new CategoryContent();
            $editRes = $categoryContent->editStatus(intval($id), $status);
            if ($editRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
        }
        $this->db->commit();
        return $this->sendJson('修改成功！');
    }
    
    /**
     * @desc 查看
     * @author: ZhaoYang
     * @date: 2018年9月24日 下午7:29:16
     */
    public function infoAction() {
        $id = $this->get('id', 'absint', 0);
        $categoryContent = new CategoryContent();
        $info = $categoryContent->getInfoById($id);
        if ($info === false) {
            $this->error($categoryContent->getMessages()[0]->getMessage(), false);
        }
        $category = new Category();
        $categoryInfo = $category->getInfoById($info->category_id);
        if($categoryInfo === false) {
            return $this->error($category->getMessages()[0]->getMessage());
        }
        $categoryContentDataInfo = CategoryContentData::findFirst('category_content_id=' . $info->id);
        $expandData = null;
        if(!empty($categoryInfo->expand_id)) {
            $expand = Expand::findFirst($categoryInfo->expand_id);
            if($expand !== false) {
                $expandData = new ExpandData($expand->table);
                $expandData = $expandData::findFirst('category_content_id=' . $info->id);
                $expandData = $expandData === false ? null : $expandData;
            }
        }
        
        $admin = new Admin();
        $translate = new Translate();
        $this->view->actionUrl = $this->url->get('categorycontent/edit');
        $this->view->actionName = $translate->t('修改');
        $this->view->actionPower = $admin->checkPower('categorycontent', 'edit');
        $this->view->categorycontentAuditPower = $admin->checkPower('categorycontent', 'audit');
        $this->view->jumpButton = $translate->t('查看修改');
        $this->view->action = 'edit';
        $this->view->categoryList = $category->getAllowList();
        $this->view->category = $categoryInfo;
        $this->view->categoryContent = $info;
        $this->view->categoryContentData = $categoryContentDataInfo;
        $this->view->expandData = $expandData;
        $this->view->positionList = (new Position())->getAllowList();
        $this->view->expandFieldList = (new ExpandField())->find('expand_id=' . $categoryInfo->expand_id);
        $this->view->categoryModel = (new CategoryModel())->findFirst($categoryInfo->category_model_id);
        $this->view->setTemplateBefore('common');
    }
    
    public function editAction() {
        $data = $this->post();
        $this->db->begin();
        $categoryContent = new CategoryContent();
        $editRes = $categoryContent->edit($data);
        if($editRes === false) {
            return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
        }
        $data['category_content_id'] = $categoryContent->id;
        $data['content'] = $this->post('content', false);
        $categoryContentData = new CategoryContentData();
        $editRes = $categoryContentData->edit($data);
        if($editRes === false) {
            $this->db->rollback();
            return $this->sendJson($categoryContentData->getMessages()[0]->getMessage(), 10001);
        }
        $category = Category::findFirst($categoryContent->category_id);
        if(!empty($category->expand_id)) {
            $expand = Expand::findFirst($category->expand_id);
            if($expand !== false) {
                $expandData = new ExpandData($expand->table);
                $editRes = $expandData->addOrEdit($this->post(null, false), $expand->id, $categoryContent->id);
                if($editRes === false) {
                    $this->db->rollback();
                    return $this->sendJson($expandData->getMessages()[0]->getMessage(), 10001);
                }
            }
        }
        $position = isset($data['position']) && is_array($data['position']) ? $data['position'] : [];
        $categoryContentPositionList = CategoryContentPosition::find('category_content_id=' . $categoryContent->id)->toArray();
        $categoryContentPositionPositionIds = array_column($categoryContentPositionList, 'position_id');
        $addData = array_values(array_diff($position, $categoryContentPositionPositionIds));
        $delData = array_values(array_diff($categoryContentPositionPositionIds, $position));
        $categoryContentPosition = new CategoryContentPosition();
        if(!empty($addData)) {
            $addRes = $categoryContentPosition->addAll($addData, $categoryContent->id);
            if($addRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContentPosition->getMessages()[0]->getMessage(), 10001);
            }
        }
        if(!empty($delData)) {
            $positionIds = implode(',', $delData);
            $delRes = $categoryContentPosition->deleteByPositionIds($positionIds);
            if($delRes === false) {
                $this->db->rollback();
                return $this->sendJson('删除推荐位失败', 10001);
            }
        }
        $keywords = empty($data['keywords']) ? [] : explode(',', $data['keywords']);
        $adminGroupInfo = $this->session->get('adminGroupInfo');
        $tagsIds = [];
        foreach ($keywords as $keyword) {
            $tags = Tags::findFirst('name="' . $keyword . '"');
            if($tags === false) {
                $tags = new Tags();
                $tags->create([
                    'name' => $keyword,
                    'language_id' => $adminGroupInfo['language_id']
                ]);
            }
            $tagsIds[] = $tags->id;
        }
        $tagsRelationList = TagsRelation::find('category_content_id=' . $categoryContent->id)->toArray();
        $tagsRelationIds = array_column($tagsRelationList, 'tags_id');
        $addTagsData = array_values(array_diff($tagsIds, $tagsRelationIds));
        $delTagsData = array_values(array_diff($tagsRelationIds, $tagsIds));
        foreach ($addTagsData as $v) {
            (new TagsRelation())->create([
                'category_content_id' => $categoryContent->id,
                'tags_id' => $v
            ]);
        }
        if(!empty($delTagsData)) {
            (new TagsRelation())->deleteById(implode(',', $delTagsData));
        }
        $this->db->commit();
        return $this->sendJson('修改成功！');
    }
    
    public function deleteAction() {
        $ids = trim($this->post('id'), ',');
        if(empty($ids)) {
            return $this->sendJson('未选中要删除的记录', 10001);
        }
        $idArr = explode(',', $ids);
        $this->db->begin();
        foreach ($idArr as $id) {
            $id = intval($id);
            $categoryContent = new CategoryContent();
            $categoryContent = $categoryContent->getInfoById($id);
            if ($categoryContent === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
            $categoryContentData = CategoryContentData::findFirst('category_content_id=' . $categoryContent->id);
            $delRes = $categoryContentData->delete();
            if ($delRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContentData->getMessages()[0]->getMessage(), 10001);
            }
            $category = Category::findFirst($categoryContent->category_id);
            if(!empty($category->expand_id)) {
                $expand = Expand::findFirst($category->expand_id);
                if($expand !== false) {
                    $expandData = new ExpandData($expand->table);
                    $delRes = $expandData->deleteByCategoryContentId($categoryContent->id);
                    if ($delRes === false) {
                        $this->db->rollback();
                        return $this->sendJson($expandData->getMessages()[0]->getMessage(), 10001);
                    }
                }
            }
            if(!empty($categoryContent->position)) {
                $categoryContentPosition = new CategoryContentPosition();
                $delRes = $categoryContentPosition->deleteByCategoryContentId($categoryContent->id);
                if ($delRes === false) {
                    $this->db->rollback();
                    return $this->sendJson($categoryContentPosition->getMessages()[0]->getMessage(), 10001);
                }
            }
            if(!empty($categoryContent->keywords)) {
                $tagsRelation = new TagsRelation();
                $delRes = $tagsRelation->deleteByCategoryContentId($categoryContent->id);
                if ($delRes === false) {
                    $this->db->rollback();
                    return $this->sendJson($tagsRelation->getMessages()[0]->getMessage(), 10001);
                }
            }
            $delRes = $categoryContent->delete();
            if ($delRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
        }
        $this->db->commit();
        return $this->sendJson('删除成功！');
    }
    
    public function moveAction() {
        $ids = trim($this->post('id'), ',');
        $categoryId = $this->post('category_id', 'absint', 0);
        if(empty($ids)) {
            return $this->sendJson('未选中要删除的记录', 10001);
        }
        $category = new Category();
        $category = $category->getInfoById($categoryId);
        if ($category === false) {
            return $this->sendJson($category->getMessages()[0]->getMessage(), 10001);
        }
        $idArr = explode(',', $ids);
        $this->db->begin();
        foreach ($idArr as $id) {
            $id = intval($id);
            $categoryContent = new CategoryContent();
            $categoryContent = $categoryContent->getInfoById($id);
            if ($categoryContent === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
            $updateRes = $categoryContent->update([
                'category_id' => $categoryId
            ]);
            if ($updateRes === false) {
                $this->db->rollback();
                return $this->sendJson($categoryContent->getMessages()[0]->getMessage(), 10001);
            }
        }
        $this->db->commit();
        return $this->sendJson('移动成功！');
    }
}