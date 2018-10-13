<?php
namespace App\Home\Controllers;

use Common\Common;
use App\Home\Models\Upload;
use App\Home\Models\Translate;
use Phalcon\Image\Adapter\Imagick;
use Phalcon\Image\Adapter\Gd;

class UeditorController extends CommonController {

    /**
     * @desc 初始化配置
     * @author: ZhaoYang
     * @date: 2018年8月16日 上午1:49:05
     */
    public function initialize() {
        $systemConfig = $this->config->system;
        $fileSize = $systemConfig->file_size * 1024 * 1024;
        $imageAllowFiles = empty($systemConfig->image_type) ? [ ] : explode(',', str_replace(',', ',.', '.' . $systemConfig->image_type));
        $videoAllowFiles = empty($systemConfig->video_type) ? [ ] : explode(',', str_replace(',', ',.', '.' . $systemConfig->video_type));
        $fileAllowFiles = empty($systemConfig->file_type) ? [ ] : explode(',', str_replace(',', ',.', '.' . $systemConfig->file_type));
        $dataPath = date('Y-m/d');
        
        $this->ueditorConfig = [ 
            // 上传图片配置项
            'imageActionName' => 'uploadimage', // 执行上传图片的action名称
            'imageFieldName' => 'upfile', // 提交的图片表单名称
            'imageMaxSize' => $fileSize, // 上传大小限制，单位B
            'imageAllowFiles' => $imageAllowFiles, // ['.png', '.jpg', '.jpeg', '.gif', '.bmp'], // 上传图片格式显示
            'imageCompressEnable' => true, // 是否压缩图片,默认是true
            'imageCompressBorder' => 1600, // 图片压缩最长边限制
            'imageInsertAlign' => 'none', // 插入的图片浮动方式
            'imageUrlPrefix' => '', // 图片访问路径前缀
            'imagePathFormat' => '/uploads/images/' . $dataPath, // 上传保存路径,可以自定义保存路径和文件名格式
            'fileNumLimit' => intval($systemConfig->file_num), // 允许批量上传数量
                                                               
            // 涂鸦图片上传配置项
            'scrawlActionName' => 'uploadscrawl', // 执行上传涂鸦的action名称
            'scrawlFieldName' => 'upfile', // 提交的图片表单名称
            'scrawlPathFormat' => '/uploads/scrawls/' . $dataPath, // 上传保存路径,可以自定义保存路径和文件名格式
            'scrawlMaxSize' => $fileSize, // 上传大小限制，单位B
            'scrawlUrlPrefix' => '', // 图片访问路径前缀
            'scrawlInsertAlign' => 'none',
            
            // 截图工具上传
            'snapscreenActionName' => 'uploadimage', // 执行上传截图的action名称
            'snapscreenPathFormat' => '/uploads/snapscreens/' . $dataPath, // 上传保存路径,可以自定义保存路径和文件名格式
            'snapscreenUrlPrefix' => '', // 图片访问路径前缀
            'snapscreenInsertAlign' => 'none', // 插入的图片浮动方式
                                               
            // 抓取远程图片配置
            'catcherLocalDomain' => [ 
                '127.0.0.1',
                'localhost',
                'img.baidu.com'
            ],
            'catcherActionName' => 'catchimage', // 执行抓取远程图片的action名称
            'catcherFieldName' => 'upfile', // 提交的图片列表表单名称
            'catcherPathFormat' => '/uploads/catchers/' . $dataPath, // 上传保存路径,可以自定义保存路径和文件名格式
            'catcherUrlPrefix' => '', // 图片访问路径前缀
            'catcherMaxSize' => $fileSize, // 上传大小限制，单位B
            'catcherAllowFiles' => $imageAllowFiles, // 抓取图片格式显示
                                                     
            // 上传视频配置
            'videoActionName' => 'uploadvideo', // 执行上传视频的action名称
            'videoFieldName' => 'upfile', // 提交的视频表单名称
            'videoPathFormat' => '/uploads/videos/' . $dataPath, // 上传保存路径,可以自定义保存路径和文件名格式
            'videoUrlPrefix' => '', // 视频访问路径前缀
            'videoMaxSize' => $fileSize, // 上传大小限制，单位B，默认100MB
            'videoAllowFiles' => $videoAllowFiles, // 上传视频格式显示
                                                   
            // 上传文件配置
            'fileActionName' => 'uploadfile', // controller里,执行上传视频的action名称
            'fileFieldName' => 'upfile', // 提交的文件表单名称
            'filePathFormat' => '/uploads/files/' . $dataPath, // 上传保存路径,可以自定义保存路径和文件名格式
            'fileUrlPrefix' => '', // 文件访问路径前缀
            'fileMaxSize' => $fileSize, // 上传大小限制，单位B，默认50MB
            'fileAllowFiles' => $fileAllowFiles, // 上传文件格式显示
                                                 
            // 列出指定目录下的图片
            'imageManagerActionName' => 'listimage', // 执行图片管理的action名称
            'imageManagerListPath' => '/uploads/images/', // 指定要列出图片的目录
            'imageManagerListSize' => 20, // 每次列出文件数量
            'imageManagerUrlPrefix' => '', // 图片访问路径前缀
            'imageManagerInsertAlign' => 'none', // 插入的图片浮动方式
            'imageManagerAllowFiles' => $imageAllowFiles, // 列出的文件类型
                                                          
            // 列出指定目录下的文件
            'fileManagerActionName' => 'listfile', // 执行文件管理的action名称
            'fileManagerListPath' => '/uploads/files/', // 指定要列出文件的目录
            'fileManagerUrlPrefix' => '', // 文件访问路径前缀
            'fileManagerListSize' => 20, // 每次列出文件数量
            'fileManagerAllowFiles' => $fileAllowFiles // 列出的文件类型
        ];
    }

    /**
     * @desc 调度器
     * @author: ZhaoYang
     * @date: 2018年8月16日 上午1:49:25
     */
    public function indexAction() {
        $action = $this->get('action');
        if($action != 'config' && !$this->config->system->upload_switch) {
            return $this->response->setJsonContent([
                'state' => '未开启上传文件功能'
            ])->send();
        }
        set_time_limit(3600);
        switch ($action) {
            case 'config':
                $response = $this->ueditorConfig;
                break;
            case 'uploadimage':
                $config = [ 
                    'allowFiles' => $this->ueditorConfig['imageAllowFiles'],
                    'maxSize' => $this->ueditorConfig['imageMaxSize'],
                    'pathFormat' => $this->ueditorConfig['imagePathFormat'],
                    'origin' => $this->get('origin', 'int!', -1),
                    'watermark_switch' => $this->post('watermark_switch', 'int!', 0),
                    'watermark_place' => $this->post('watermark_place', 'int!', 0),
                    'thumbnail_switch' => $this->post('thumbnail_switch', 'int!', 0),
                    'thumbnail_maxwidth' => $this->post('thumbnail_maxwidth', 'int!', 210),
                    'thumbnail_maxheight' => $this->post('thumbnail_maxheight', 'int!', 110),
                    'thumbnail_cutout' => $this->post('thumbnail_cutout', 'int!', 1),
                ];
                $response = $this->uploadfile($config);
                break;
            case 'uploadvideo':
                $config = [ 
                    'allowFiles' => $this->ueditorConfig['videoAllowFiles'],
                    'maxSize' => $this->ueditorConfig['videoMaxSize'],
                    'pathFormat' => $this->ueditorConfig['videoPathFormat'],
                    'origin' => $this->get('origin', 'int!', -1)
                ];
                $response = $this->uploadfile($config);
                break;
            case 'uploadfile':
                $config = [ 
                    'allowFiles' => $this->ueditorConfig['fileAllowFiles'],
                    'maxSize' => $this->ueditorConfig['fileMaxSize'],
                    'pathFormat' => $this->ueditorConfig['filePathFormat'],
                    'origin' => $this->get('origin', 'int!', -1),
                ];
                $response = $this->uploadfile($config);
                break;
            case 'uploadscrawl':
                $config = [ 
                    'allowFiles' => $this->ueditorConfig['imageAllowFiles'],
                    'maxSize' => $this->ueditorConfig['scrawlMaxSize'],
                    'pathFormat' => $this->ueditorConfig['scrawlPathFormat'],
                    'base64Data' =>$this->post($this->ueditorConfig['scrawlFieldName']),
                    'fileName' =>$this->post('fileName'),
                    'origin' => $this->get('origin', 'int!', -1)
                ];
                $response = $this->uploadBase64($config);
                break;
            case 'listimage':
                $config = [ 
                    'allowFiles' => $this->ueditorConfig['imageManagerAllowFiles'],
                    'maxSize' => $this->ueditorConfig['imageMaxSize'],
                    'listPath' => $this->ueditorConfig['imageManagerListPath'],
                    'size' => $this->ueditorConfig['imageManagerListSize'],
                    'start' => $this->get('start', 'int!', 0)
                ];
                $response = $this->listfile($config);
                break;
            case 'listfile':
                $config = [ 
                    'allowFiles' => $this->ueditorConfig['fileManagerAllowFiles'],
                    'maxSize' => $this->ueditorConfig['fileMaxSize'],
                    'listPath' => $this->ueditorConfig['fileManagerListPath'],
                    'size' => $this->ueditorConfig['fileManagerListSize'],
                    'start' => $this->get('start', 'int!', 0)
                ];
                $response = $this->listfile($config);
                break;
            default:
                $response = [ 
                    'state' => '请求地址出错'
                ];
        }
        return $this->response->setJsonContent($response)->send();
    }

    /** 
     * @desc 上传文件
     * @author ZhaoYang 
     * @date 2018年8月16日 下午4:46:12 
     */
    private function uploadfile(array $config) {
        if (!$this->request->hasFiles()) {
            $state = '未上传文件';
            goto error_response;
        }
        $files = $this->request->getUploadedFiles();
        $file = $files[0];
        if (!$file->isUploadedFile()) {
            $state = '临时文件错误';
            goto error_response;
        }
        $type = '.' . strtolower(substr($file->getType(), 6));
        $extension = '.' . strtolower($file->getExtension());
        if (!in_array($type, $config['allowFiles']) && !in_array($extension, $config['allowFiles'])) {
            $state = '文件类型错误';
            goto error_response;
        }
        $size = $file->getSize();
        if ($size > $config['maxSize']) {
            $state = '文件大小超出限制';
            goto error_response;
        }
        $path = PUBLIC_PATH . $config['pathFormat'];
        if (!Common::mkdir($path)) {
            $state = '创建目录失败';
            goto error_response;
        } else if (!is_writeable($path)) {
            $state = $config['pathFormat'] . '该目录无写入权限';
            goto error_response;
        }
        $original = $file->getName();
        $name = preg_replace('/[\|\?\"\<\>\/\*\\\\]+/', '', $original);
        $i = 1;
        rename:
        $destination = $path . '/' . $name;
        $destination = strstr(PHP_OS, 'WIN') ? (function_exists('mb_convert_encoding') ? mb_convert_encoding($destination, 'GBK', 'UTF-8') : iconv('UTF-8', 'GBK', $destination)) : $destination;
        $imageUrl = $config['pathFormat'] . '/' . $name;
        if (is_file($destination)) {
            if (md5(file_get_contents($file->getTempName())) == md5(file_get_contents($destination))) {
                goto thumbnail_image;
            }
            $name = basename($name, ($i == 1 ? '' : $i - 1) . $extension) . $i ++ . $extension;
            goto rename;
        }
        if (!$file->moveTo($destination)) {
            $state = '移动文件失败';
            goto error_response;
        }
        (new Upload())->create([
            'file' => $imageUrl,
            'folder' => $config['pathFormat'] . '/',
            'title' => basename($name, $extension),
            'ext' => ltrim($extension, '.'),
            'size' => $size,
            'type' => $file->getType(),
            'time' => date('Y-m-d H:i:s'),
            'module' => $config['origin']
        ]);
        thumbnail_image:
        $thumbnailUrl = '';
        if(strpos(strtolower($file->getType()), 'image') !== false) {
            $thumbnailUrl = $imageUrl;
            if($config['thumbnail_switch']) {
                $image = $this->getImage($destination);
                if($image === false) {
                    $state = '未找到Imagick和Gd，或版本过低';
                    goto error_response;
                }
                $thumbnailName = basename($name, $extension) . '_thumbnail' . $extension;
                $thumbnailDestination = $path . '/' . $thumbnailName;
                $thumbnailDestination = strstr(PHP_OS, 'WIN') ? (function_exists('mb_convert_encoding') ? mb_convert_encoding($thumbnailDestination, 'GBK', 'UTF-8') : iconv('UTF-8', 'GBK', $thumbnailDestination)) : $thumbnailDestination;
                $imageWidth = $image->getWidth();
                $imageHeight = $image->getHeight();
                $width = $config['thumbnail_maxwidth'] <=0 ? 210 : intval($config['thumbnail_maxwidth']);
                $height = $config['thumbnail_maxheight'] <=0 ? 110 : intval($config['thumbnail_maxheight']);
                if($config['thumbnail_cutout']) {
                    $image->crop($width, $height);
                } else {
                    $width = intval(($width/100) * $imageWidth);
                    $height = intval(($height/100) * $imageHeight);
                    $image->resize($width, $height);
                }
                if (!$image->save($thumbnailDestination)) {
                    $state = '生成缩略图失败';
                    goto error_response;
                }
                (new Upload())->create([
                    'file' => $config['pathFormat'] . '/' . $thumbnailName,
                    'folder' => $config['pathFormat'] . '/',
                    'title' => basename($thumbnailName, $extension),
                    'ext' => ltrim($extension, '.'),
                    'size' => filesize($thumbnailDestination),
                    'type' => mime_content_type($thumbnailDestination),
                    'time' => date('Y-m-d H:i:s'),
                    'module' => $config['origin']
                ]);
                $thumbnailUrl = $config['pathFormat'] . '/' . $thumbnailName;
            }
        }
        if($config['watermark_switch']) {
            $image = $this->getImage($destination);
            if($image === false) {
                $state = '未找到Imagick和Gd，或版本过低';
                goto error_response;
            }
            $watermarkImagePath = PUBLIC_PATH . $this->config->system->watermark_image;
            if(!file_exists($watermarkImagePath)) {
                $state = '水印图片不存在';
                goto error_response;
            }
            $watermarkImage = $this->getImage($watermarkImagePath);
            $imageWidth = $image->getWidth();
            $imageHeight = $image->getHeight();
            $watermarkImageWidth = $watermarkImage->getWidth();
            $watermarkImageHeight = $watermarkImage->getHeight();
            $subWidth = intval($imageWidth - $watermarkImageWidth);
            $subHeight = intval($imageHeight - $watermarkImageHeight);
            switch($config['watermark_place']) {
                case 1:
                    $offsetX = $offsetY = 0;
                    break;
                case 2:
                    $offsetX = intval($subWidth/2);
                    $offsetY = 0;
                    break;
                case 3:
                    $offsetX = $subWidth;
                    $offsetY = 0;
                    break;
                case 4:
                    $offsetX = 0;
                    $offsetY = intval($subHeight/2);
                    break;
                case 5:
                    $offsetX = intval($subWidth/2);
                    $offsetY = intval($subHeight/2);
                    break;
                case 6:
                    $offsetX = $subWidth;
                    $offsetY = intval($subHeight/2);
                    break;
                case 7:
                    $offsetX = 0;
                    $offsetY = $subHeight;
                    break;
                case 8:
                    $offsetX = intval($subWidth/2);
                    $offsetY = $subHeight;
                    break;
                case 9:
                    $offsetX = $subWidth;
                    $offsetY = $subHeight;
                    break;
                default:
                    $offsetX = mt_rand(0, $subWidth);
                    $offsetY = mt_rand(0, $subHeight);
                    break;
            }
            $image->watermark($watermarkImage, $offsetX, $offsetY);
            if (!$image->save()) {
                $state = '生成水印失败';
                goto error_response;
            }
        }
        return [
            'original' => $original,
            'size' => $size,
            'state' => 'SUCCESS',
            'title' => basename($name, $extension),
            'type' => $extension,
            'url' => $imageUrl,
            'thumbnail_url' => $thumbnailUrl,
        ];
        $state = '上传失败';
        error_response:
        return [ 
            'state' => (new Translate())->t($state)
        ];
    }
    
    /**
     * @desc 获取image对象
     * @param string $destination 图片路径
     * @return Phalcon\Image\Adapter|bool
     * @author: ZhaoYang
     * @date: 2018年8月24日 下午10:36:52
     */
    private function getImage(string $destination) {
        try {
            $image = new Imagick($destination);
            if(!$image->check()) {
                throw new \Exception('校验不通过');
            }
        } catch (\Exception $e) {
            $image = new Gd($destination);
            try {
                $image = new Gd($destination);
                if(!$image->check()) {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return $image;
    }

    /**
     * @desc base64编码图片上传
     * @author: ZhaoYang
     * @date: 2018年8月17日 下午10:15:20
     */
    private function uploadBase64(array $config) {
        $base64Data = $config['base64Data'];
        if (empty($base64Data)) {
            $state = '未上传文件';
            goto error_response;
        }
        $img = base64_decode($base64Data);
        if ($img === false) {
            $state = '解析文件错误';
            goto error_response;
        }
        $fileSize = strlen($img);
        if ($fileSize > $config['maxSize']) {
            $state = '文件大小超出限制';
            goto error_response;
        }
        $path = PUBLIC_PATH . $config['pathFormat'];
        if (!Common::mkdir($path)) {
            $state = '创建目录失败';
            goto error_response;
        } else if (!is_writeable($path)) {
            $state = $config['pathFormat'] . '该目录无写入权限';
            goto error_response;
        }
        $original = isset($config['fileName']) ? $config['fileName'] : md5(microtime(true) . mt_rand(1000, 9999)) . '.png';
        $fileName = preg_replace('/[\|\?\"\<\>\/\*\\\\]+/', '', $original);
        $extension = '.' . strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $config['allowFiles'])) {
            $state = '文件类型错误';
            goto error_response;
        }
        $destination = $path . '/' . $fileName;
        $destination = strstr(PHP_OS, 'WIN') ? (function_exists('mb_convert_encoding') ? mb_convert_encoding($destination, 'GBK', 'UTF-8') : iconv('UTF-8', 'GBK', $destination)) : $destination;
        if (file_put_contents($destination, $img)) {
            if(!function_exists('finfo_open')) {
                $state = '请开启php_fileinfo.dll扩展';
                goto error_response;
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $destination);
            (new Upload())->create([ 
                'file' => $config['pathFormat'] . '/' . $fileName,
                'folder' => $config['pathFormat'] . '/',
                'title' => pathinfo($fileName, PATHINFO_FILENAME),
                'ext' => ltrim($extension, '.'),
                'size' => $fileSize,
                'type' => $mime,
                'time' => date('Y-m-d H:i:s'),
                'module' => $config['origin']
            ]);
            return [ 
                'original' => $original,
                'size' => $fileSize,
                'state' => 'SUCCESS',
                'title' => $fileName,
                'type' => '.' . $extension,
                'url' => $config['pathFormat'] . '/' . $fileName
            ];
        }
        $state = '上传失败';
        error_response:
        return [ 
            'state' => (new Translate())->t($state)
        ];
    }

    /**
     * @desc 获取本地图片列表
     * @author: ZhaoYang
     * @date: 2018年8月18日 下午10:30:38
     */
    private function listfile(array $config) {
        $path = PUBLIC_PATH . $config['listPath'];
        $list = [ ];
        $state = 'SUCCESS';
        if (!is_dir($path) || empty($config['allowFiles'])) {
            $total = 0;
            goto success_response;
        }
        $handle = opendir($path);
        if ($handle === false) {
            $state = $config['listPath'] . '目录读取失败';
            goto error_response;
        }
        $allowFiles = str_replace('.', '|', ltrim(implode('', $config['allowFiles']), '.'));
        $files = Common::getFiles($path, '/.+(' . $allowFiles . ')/i', true);
        $total = count($files);
        $files = array_reverse($files);
        $files = array_splice($files, $config['start'], $config['size']);
        $list = [ ];
        $substrStart = strlen(PUBLIC_PATH);
        foreach ($files as $file) {
            if (filesize($file) > $config['maxSize']) {
                continue;
            }
            $list[] = [ 
                'url' => substr($file, $substrStart),
                'mtime' => filemtime($file)
            ];
        }
        success_response:
        return [ 
            'state' => (new Translate())->t($state),
            'list' => $list,
            'start' => $config['start'],
            'total' => $total
        ];
        error_response:
        return [ 
            'state' => (new Translate())->t($state)
        ];
    }
    
    /**
     * @desc 获取上传模板
     * @author: ZhaoYang
     * @date: 2018年8月19日 下午9:52:45
     */
    public function getUpfileHtmlAction() {
        switch ($this->request('type')) {
            case 'file':
                $renderView = 'ueditor/file';
                break;
            case 'image':
                $renderView = 'ueditor/image';
                $this->view->fieldName = $this->ueditorConfig['scrawlFieldName'];
                break;
            case 'images':
                $renderView = 'ueditor/images';
                break;
            default:
                return;
        }
        $this->view->pick($renderView);
        $this->view->origin = $this->request('origin', 'int!', -1);
        $this->view->id = $this->request('id');
    }
}