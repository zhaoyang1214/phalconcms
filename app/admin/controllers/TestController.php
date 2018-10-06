<?php
namespace App\Admin\Controllers;

// use App\Admin\Models\Translate;
use Library\Vendors\Pinyin\Pinyin;

class TestController extends CommonController {

    public function testAction() {
//         $res = (new Translate())->t('你好', 'en');
//         var_dump($res);
//         exit();
        
        $res = (new Pinyin())->permalink($this->get('text')??'带着希望去旅行', '');
        var_dump($res);
        exit();
    }

}