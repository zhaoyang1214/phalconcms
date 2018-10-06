
#语言表
DROP TABLE IF EXISTS `ph_language`;
CREATE TABLE `ph_language` (
	`id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) UNIQUE NOT NULL COMMENT '各语言名称',
	`zh_name` varchar(100) UNIQUE NOT NULL COMMENT '中文名称',
	`lang` varchar(20) UNIQUE NOT NULL COMMENT '英文简称，标识',
	`theme` varchar(20) NOT NULL COMMENT '前台视图主题',
	`admin_theme` varchar(20) NOT NULL COMMENT '后台视图主题',
	`domain` varchar(50) NOT NULL DEFAULT '' COMMENT '域名，唯一',
	`status` tinyint NOT NULL DEFAULT '0' COMMENT '是否启用，0：禁用，1：启用',
	PRIMARY KEY (`id`),
	KEY `domain`(`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='语言表';

TRUNCATE `ph_language`;
INSERT INTO `ph_language` VALUES
(1, '中文', '中文', 'zh', 'zh', 'zh', '', 1),
(2, 'English', '英文', 'en', 'en', 'zh', '', 0),
(3, '日本語', '日文', 'ja', 'ja', 'zh', '', 0),
(4, '한글', '韩文', 'ko', 'ko', 'zh', '', '0');

#翻译表
DROP TABLE IF EXISTS `ph_translate`;
CREATE TABLE `ph_translate` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`sign` char(32) UNIQUE NOT NULL COMMENT '原文+"to"+language表标识（lang）后md5',
	`source_text` varchar(255) NOT NULL COMMENT '原文',
	`source_language_id` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '原文language表 id',
	`translated_text` varchar(255) NOT NULL COMMENT '译文',
	`translated_language_id` tinyint UNSIGNED NOT NULL COMMENT '译文language表 id',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='翻译表';

#翻译驱动表
DROP TABLE IF EXISTS `ph_translate_driver`;
CREATE TABLE `ph_translate_driver` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) UNIQUE NOT NULL COMMENT '驱动名称',
	`class_name` varchar(100) NOT NULL COMMENT '类名（包含命名空间）',
	`config` varchar(2000) NOT NULL default '' COMMENT '配置',
	`status` tinyint NOT NULL DEFAULT '0' COMMENT '是否启用，0：禁用，1：启用',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='翻译驱动表';

INSERT INTO `ph_translate_driver` VALUES ('1', '百度', 'Library\\Translate\\Adapter\\Baidu', '', '0');

#权限表
DROP TABLE IF EXISTS `ph_admin_auth`;
CREATE TABLE `ph_admin_auth` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL COMMENT '权限名称',
	`pid` smallint UNSIGNED NOT NULL COMMENT '父id',
	`controller` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器',
	`action` varchar(30)  NOT NULL DEFAULT '' COMMENT '操作方法',
	`sequence` smallint NOT NULL DEFAULT '0' COMMENT '排序，越小越排在前面',
	`note` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
	`icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
	`status` tinyint NOT NULL DEFAULT '1' COMMENT '状态：0：隐藏，1：显示',
	PRIMARY KEY (`id`),
	KEY `pid` (`pid`, `status`),
	KEY `auth_ca` (`controller`,`action`, `status`),
	KEY `sequence` (`sequence`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='权限表';

TRUNCATE `ph_admin_auth`;
INSERT INTO `ph_admin_auth` VALUES
(1, '首页', 0, 'Index', 'manage', 0, '首页操作', '', 1),
(2, '栏目', 0, 'Category', 'manage', 5, '栏目操作', '', 1),
(3, '内容', 0, 'Categorycontent', 'manage', 10, '内容操作', '', 1),
(4, '扩展', 0, 'Expand', 'manage', 15, '扩展操作', '', 1),
(5, '表单', 0, 'Form', 'manage', 20, '表单操作', '', 1),
(6, '用户', 0, 'Admin', 'manage', 25, '用户操作', '', 1),

(100, '后台首页', 1, 'Index', 'home', 100, '后台首页浏览', '', 1),
(101, '系统设置', 1, 'Systemset', 'index', 105, '系统设置浏览', '', 1),
(102, '模型管理', 1, 'Categorymodel', 'index', 110, '模型管理浏览', '', 1),
(103, '插件管理', 1, 'Plugin', 'index', 115, '插件管理浏览', '', 0),
(104, '语言管理', 1, 'Language', 'index', 120, '语言管理浏览', '', 1),
(105, '翻译管理', 1, 'Translate', 'index', 125, '翻译管理浏览', '', 1),
(106, '翻译驱动管理', 1, 'Translatedriver', 'index', 130, '翻译驱动浏览', '', 1),
(107, '管理组管理', 6, 'Admingroup', 'index', 135, '管理组浏览', '', 1),
(108, '管理员管理', 6, 'Admin', 'index', 140, '管理员浏览', '', 1),
(109, '后台登录记录', 6, 'Adminlog', 'index', 145, '后台登录记录浏览', '', 1),
(110, '表单设置', 5, 'Form', 'index', 150, '多用表单浏览', '', 1),
(111, '扩展模型', 4, 'Expand', 'index', 155, '扩展模型浏览', '', 1),
(112, '自定义变量', 4, 'Fragment', 'index', 160, '自定义变量浏览', '', 1),
(113, '内容替换', 4, 'Replace', 'index', 165, '内容替换浏览', '', 1),
(114, 'TAG管理', 4, 'tags', 'index', 170, 'TAG管理浏览', '', 1),
(115, '推荐位管理', 4, 'Position', 'index', 175, '推荐位管理浏览', '', 1),
(116, '附件管理', 4, 'Upload', 'index', 180, '附件管理浏览', '', 1),
(117, '栏目管理', 2, 'Category', 'index', 185, '栏目浏览', '', 1),
(118, '内容首页', 3, 'Categorycontent', 'list', 190, '内容首页浏览', '', 1),

(1000, '保存', 101, 'Systemset', 'save', 1000, '系统设置保存', '', 1),
(1001, '添加语言', 104, 'Language', 'add', 1005, '语言管理添加', '', 1),
(1002, '查看', 104, 'Language', 'info', 1010, '语言管理查看', '', 1),
(1003, '修改', 104, 'Language', 'edit', 1015, '语言管理修改', '', 1),
(1004, '查看', 105, 'Translate', 'info', 1025, '翻译查看', '', 1),
(1005, '修改', 105, 'Translate', 'edit', 1030, '翻译修改', '', 1),
(1006, '添加翻译驱动', 106, 'Translatedriver', 'add', 1035, '翻译驱动添加', '', 1),
(1007, '查看', 106, 'Translatedriver', 'info', 1040, '翻译驱动查看', '', 1),
(1008, '修改', 106, 'Translatedriver', 'edit', 1045, '翻译驱动修改', '', 1),
(1009, '添加管理组', 107, 'Admingroup', 'add', 1050, '管理组添加', '', 1),
(1010, '查看', 107, 'Admingroup', 'info', 1055, '管理组查看', '', 1),
(1011, '修改', 107, 'Admingroup', 'edit', 1060, '管理组修改', '', 1),
(1012, '删除', 107, 'Admingroup', 'delete', 1065, '管理组删除', '', 1),
(1013, '添加管理员', 108, 'Admin', 'add', 1050, '管理员添加', '', 1),
(1014, '查看', 108, 'Admin', 'info', 1055, '管理员查看', '', 1),
(1015, '设置', 108, 'Admin', 'edit', 1060, '管理员设置', '', 1),
(1016, '修改资料', 108, 'Admin', 'editInfo', 1065, '管理员修改资料', '', 1),
(1017, '删除', 108, 'Admin', 'delete', 1070, '管理员删除', '', 1),
(1018, '添加表单', 110, 'Form', 'add', 1075, '表单添加', '', 1),
(1019, '查看', 110, 'Form', 'info', 1080, '表单查看', '', 1),
(1020, '修改', 110, 'Form', 'edit', 1085, '表单修改', '', 1),
(1021, '删除', 110, 'Form', 'delete', 1090, '表单删除', '', 1),
(1022, '字段管理', 110, 'Formfield', 'index', 1095, '表单字段管理', '', 1),
(1023, '表单数据管理', 110, 'Formdata', 'index', 1100, '浏览用户创建的表单', '', 1),
(1024, '添加模型', 111, 'Expand', 'add', 1105, '模型添加', '', 1),
(1025, '查看', 111, 'Expand', 'info', 1110, '模型查看', '', 1),
(1026, '修改', 111, 'Expand', 'edit', 1115, '模型修改', '', 1),
(1027, '删除', 111, 'Expand', 'delete', 1120, '模型删除', '', 1),
(1028, '字段管理', 111, 'Expandfield', 'index', 1125, '模型字段管理', '', 1),
(1029, '添加自定义变量', 112, 'Fragment', 'add', 1130, '添加自定义变量', '', 1),
(1030, '查看', 112, 'Fragment', 'info', 1135, '自定义变量查看', '', 1),
(1031, '修改', 112, 'Fragment', 'edit', 1140, '自定义变量修改', '', 1),
(1032, '删除', 112, 'Fragment', 'delete', 1145, '自定义变量删除', '', 1),
(1033, '添加内容替换', 113, 'Replace', 'add', 1150, '添加内容替换', '', 1),
(1034, '查看', 113, 'Replace', 'info', 1155, '内容替换查看', '', 1),
(1035, '修改', 113, 'Replace', 'edit', 1160, '内容替换修改', '', 1),
(1036, '删除', 113, 'Replace', 'delete', 1175, '内容替换删除', '', 1),
(1037, '添加TAG', 114, 'tags', 'add', 1180, '添加tag标签', '', 1),
(1038, '分组', 114, 'tags', 'grouping', 1185, 'tag标签分组', '', 1),
(1039, '删除', 114, 'tags', 'delete', 1190, 'tag标签删除', '', 1),
(1040, 'TAG分组管理', 114, 'tagsgroup', 'index', 1195, 'tag标签分组管理', '', 1),
(1041, '添加推荐位', 115, 'Position', 'add', 1200, '添加推荐位', '', 1),
(1042, '查看', 115, 'Position', 'info', 1205, '推荐位查看', '', 1),
(1043, '修改', 115, 'Position', 'edit', 1210, '推荐位修改', '', 1),
(1044, '删除', 115, 'Position', 'delete', 1215, '推荐位删除', '', 1),
(1045, '删除', 116, 'Upload', 'delete', 1220, '附件删除', '', 1),
(1046, '栏目排序', 117, 'Category', 'sequence', 1225, '栏目排序', '', 1),
(1047, '添加新闻栏目', 117, 'categorynews', 'add', 1230, '添加新闻栏目', '', 1),
(1048, '查看新闻栏目', 117, 'categorynews', 'info', 1230, '查看新闻栏目', '', 1),
(1049, '修改新闻栏目', 117, 'categorynews', 'edit', 1230, '修改新闻栏目', '', 1),
(1050, '删除新闻栏目', 117, 'categorynews', 'delete', 1230, '删除新闻栏目', '', 1),
(1051, '添加页面栏目', 117, 'categorypage', 'add', 1230, '添加页面栏目', '', 1),
(1052, '查看页面栏目', 117, 'categorypage', 'info', 1230, '查看页面栏目', '', 1),
(1053, '修改页面栏目', 117, 'categorypage', 'edit', 1230, '修改页面栏目', '', 1),
(1054, '删除页面栏目', 117, 'categorypage', 'delete', 1230, '删除页面栏目', '', 1),
(1055, '添加跳转栏目', 117, 'categoryjump', 'add', 1230, '添加跳转栏目', '', 1),
(1056, '查看跳转栏目', 117, 'categoryjump', 'info', 1230, '查看跳转栏目', '', 1),
(1057, '修改跳转栏目', 117, 'categoryjump', 'edit', 1230, '修改跳转栏目', '', 1),
(1058, '删除跳转栏目', 117, 'categoryjump', 'delete', 1230, '删除跳转栏目', '', 1),
(1059, '审核', 118, 'Categorycontent', 'audit', 1235, '审核内容', '', 1),
(1060, '添加内容', 118, 'Categorycontent', 'add', 1240, '添加内容', '', 1),
(1061, '查看内容', 118, 'Categorycontent', 'info', 1245, '查看内容', '', 1),
(1062, '修改内容', 118, 'Categorycontent', 'edit', 1250, '修改内容', '', 1),
(1063, '删除内容', 118, 'Categorycontent', 'delete', 1255, '删除内容', '', 1),
(1064, '移动', 118, 'Categorycontent', 'move', 1260, '移动内容到某栏目', '', 1),
(1065, '内容管理', 118, 'Categorycontent', 'index', 1234, '浏览某个栏目的内容', '', 1),
(1066, '快速编辑', 118, 'Categorycontent', 'quickEdit', 1251, '快速编辑内容', '', 1),
(1067, '查看', 102, 'Categorymodel', 'info', 1060, '模型查看', '', 1),
(1068, '修改', 102, 'Categorymodel', 'edit', 1065, '模型配置', '', 1),

(10000, '添加', 1022, 'Formfield', 'add', 10000, '添加表单字段', '', 1),
(10001, '查看', 1022, 'Formfield', 'info', 10005, '查看表单字段', '', 1),
(10002, '修改', 1022, 'Formfield', 'edit', 10010, '修改表单字段', '', 1),
(10003, '删除', 1022, 'Formfield', 'delete', 10015, '删除表单字段', '', 1),
(10004, '添加', 1023, 'Formdata', 'add', 10020, '添加表单数据', '', 1),
(10005, '查看', 1023, 'Formdata', 'info', 10025, '查看表单数据', '', 1),
(10006, '修改', 1023, 'Formdata', 'edit', 10030, '修改表单数据', '', 1),
(10007, '删除', 1023, 'Formdata', 'delete', 10035, '删除表单数据', '', 1),
(10008, '添加', 1028, 'Expandfield', 'add', 10040, '添加模型字段', '', 1),
(10009, '查看', 1028, 'Expandfield', 'info', 10045, '查看模型字段', '', 1),
(10010, '修改', 1028, 'Expandfield', 'edit', 10050, '修改模型字段', '', 1),
(10011, '删除', 1028, 'Expandfield', 'delete', 10055, '删除模型字段', '', 1),
(10012, '添加', 1040, 'tagsgroup', 'add', 10060, '添加tag标签分组', '', 1),
(10013, '查看', 1040, 'tagsgroup', 'info', 10065, '查看tag标签分组', '', 1),
(10014, '修改', 1040, 'tagsgroup', 'edit', 10070, '修改tag标签分组', '', 1),
(10015, '删除', 1040, 'tagsgroup', 'delete', 10085, '删除tag标签分组', '', 1);

#用户组表
DROP TABLE IF EXISTS `ph_admin_group`;
CREATE TABLE `ph_admin_group` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) UNIQUE NOT NULL COMMENT '角色名称',
	`admin_auth_ids` varchar(3000) NOT NULL DEFAULT '' COMMENT '操作权限ids,1,2,5',
	`category_ids` varchar(5000) NOT NULL DEFAULT '' COMMENT '栏目权限',
	`form_ids` varchar(5000) NOT NULL DEFAULT '' COMMENT '表单权限',
	`grade` tinyint NOT NULL DEFAULT '1' COMMENT '等级',
	`keep` tinyint NOT NULL DEFAULT '0' COMMENT '是否校验权限（允许组合），0：全部校验，1：不校验表单权限，2：不校验栏目权限，4：不校验功能权限，7：全部不校验',
	`group_power` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理组列表权限（只允许增删改下级管理组）（允许组合），1：显示本组，2：显示同级别组，4：显示低级别组',
	`admin_power` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员列表权限（只允许增删改下级管理员（修改资料除外））（允许组合）（这里的修改只针对资料），1：显示管理员本人,2：显示同级别组管理员，4：显示低级别组管理员，8：修改管理员本人，16：修改同级别组管理员，32：修改低级别组管理员',
	`language_power` tinyint NOT NULL DEFAULT '1' COMMENT '是否受语言限制（不受限制可以浏览添加修改他国语言管理组、管理员），1：受限制，0：不受限制',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表 id',
	PRIMARY KEY (`id`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户组表';

TRUNCATE `ph_admin_group`;
INSERT INTO `ph_admin_group` VALUES
(1, '超级管理组', '', '', '', 1, 7, 7, 63, 0, 1),
(2, '管理员组', '1,100,101,1000,102,1067,1068,103,104,1001,1002,1003,105,1004,1005,106,1006,1007,1008,2,117,1046,1047,1048,1049,1050,1051,1052,1053,1054,1055,1056,1057,1058,3,118,1065,1059,1060,1061,1062,1066,1063,1064,4,111,1024,1025,1026,1027,1028,10008,10009,10010,10011,112,1029,1030,1031,1032,113,1033,1034,1035,1036,114,1037,1038,1039,1040,10012,10013,10014,10015,115,1041,1042,1043,1044,116,1045,5,110,1018,1019,1020,1021,1022,10000,10001,10002,10003,1023,10004,10005,10006,10007,6,107,1009,1010,1011,1012,108,1013,1014,1015,1016,1017,109', '', '', 10, 3, 7, 63, 0, 1);

#管理员表
DROP TABLE IF EXISTS `ph_admin`;
CREATE TABLE `ph_admin` (
	`id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` varchar(20) UNIQUE NOT NULL COMMENT '用户名',
	`password` varchar(32) NOT NULL COMMENT '密码',
	`nicename` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
	`regtime` datetime NOT NULL COMMENT '注册时间',
	`status` tinyint NOT NULL DEFAULT '1' COMMENT '状态：1：正常，0：禁用',
	`admin_group_id` smallint UNSIGNED NOT NULL COMMENT 'admin_group表 id',
	PRIMARY KEY (`id`),
	KEY `admin_group_id` (`admin_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='管理员表';

TRUNCATE `ph_admin`;
INSERT INTO `ph_admin` VALUES
(1, 'admin', '324db4c1c67e07dd3ed660214d1cdeff', '超级管理员', '2018-07-01 00:00:00', 1, 1),
(2, 'phalcon', '324db4c1c67e07dd3ed660214d1cdeff', '管理员', '2018-07-14 00:00:00', 1, 2);

#管理员登录记录表
DROP TABLE IF EXISTS `ph_admin_log`;
CREATE TABLE `ph_admin_log` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`admin_id` mediumint UNSIGNED NOT NULL COMMENT '表admin id',
	`logintime` datetime NOT NULL COMMENT '登录时间',
	`ip` varchar(64) NOT NULL DEFAULT '' COMMENT 'ip地址',
	PRIMARY KEY (`id`),
	KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='管理员登录记录表';

#系统配置
DROP TABLE IF EXISTS `ph_system_config`;
CREATE TABLE `ph_system_config` (
	`id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
	`config` varchar(5000) NOT NULL COMMENT '配置',
	`language_id` tinyint UNSIGNED UNIQUE NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='系统设置';

#多功能表单表
DROP TABLE IF EXISTS `ph_form`;
CREATE TABLE `ph_form` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) UNIQUE NOT NULL DEFAULT '' COMMENT '表单名称',
	`no` char(32) UNIQUE NOT NULL COMMENT '表单编号',
	`table` varchar(20) UNIQUE NOT NULL DEFAULT '' COMMENT '表单名',
	`sort` varchar(20) NOT NULL DEFAULT 'id DESC' COMMENT '内容排序',
	`display` tinyint NOT NULL DEFAULT '0' COMMENT '是否在前台显示此表单的分页列表内容，0：否，1：是',	
	`page` smallint NOT NULL DEFAULT '10' COMMENT '前台分页数',
	`alone_tpl` tinyint NOT NULL DEFAULT '0' COMMENT '是否使用独立模板，0：否，1：是',
	`tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '前台模板',	
	`where` varchar(255) NOT NULL DEFAULT '' COMMENT '前台分页条件',
	`return_type` tinyint NOT NULL DEFAULT '0' COMMENT '提交表单返回类型，0：JS消息框，1：json',
	`return_msg` varchar(255) NOT NULL DEFAULT '提交成功' COMMENT '提交成功后返回的提示信息',
	`return_url` varchar(255) NOT NULL DEFAULT '' COMMENT '提交成功后跳转的地址',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	`is_captcha` tinyint NOT NULL DEFAULT '0' COMMENT '是否使用图片验证码，0：否，1：是',
	PRIMARY KEY (`id`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='多功能表单表';

#多功能表单字段表
DROP TABLE IF EXISTS `ph_form_field`;
CREATE TABLE `ph_form_field` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`form_id` int UNSIGNED NOT NULL COMMENT 'form表id',
	`name` varchar(50) NOT NULL COMMENT '字段描述',
	`field` varchar(50) NOT NULL COMMENT '字段名',
	`type` tinyint NOT NULL DEFAULT '1' COMMENT '字段类型，1：文本框；2：多行文本；3：编辑器，4：文件上传；5：单图片上传；6：组图上传；7：下拉菜单；8：单选；9：多选',
	`property` tinyint NOT NULL DEFAULT '1' COMMENT '字段属性，1：varchar；2：int；3：text；4：datetime；5：decimal；',
	`len` smallint NOT NULL DEFAULT '0' COMMENT '字段长度',
	`decimal` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '小数点位数',
	`default` varchar(255) NOT NULL DEFAULT '' COMMENT '默认值',
	`sequence` smallint NOT NULL DEFAULT '0' COMMENT '排序，越小越排在前面',
	`tip` varchar(255) NOT NULL DEFAULT '' COMMENT '字段提示',
	`config` varchar(255) NOT NULL DEFAULT '' COMMENT '字段配置',
	`is_must` tinyint NOT NULL DEFAULT '0' COMMENT '是否必填，0：否，1：是',
	`is_unique` tinyint NOT NULL DEFAULT '0' COMMENT '是否唯一，0：否，1：是',
	`admin_display` tinyint NOT NULL DEFAULT '0' COMMENT '是否后台显示，0：否，1：是',
	`admin_display_len` smallint NOT NULL DEFAULT '0' COMMENT '后台列表显示长度',
	PRIMARY KEY (`id`),
	UNIQUE KEY `form_id_name` (`form_id`, `name`),
	UNIQUE KEY `form_id_field` (`form_id`, `field`),
	KEY `sequence` (`sequence`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='多功能表单字段表';

#上传文件表
DROP TABLE IF EXISTS `ph_upload`;
CREATE TABLE `ph_upload` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`file` varchar(255) NOT NULL COMMENT '文件',
	`folder` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
	`title` varchar(255) NOT NULL COMMENT '文件名',
	`ext` varchar(20) NOT NULL DEFAULT '' COMMENT '文件扩展名',
	`size` int UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件大小',
	`type` varchar(255) NOT NULL DEFAULT '' COMMENT '文件类型',
	`time` datetime NOT NULL COMMENT '上传时间',
	`module` tinyint NOT NULL DEFAULT '-1'  COMMENT '所属模块，-1:未绑定模块；1：栏目模块，2：内容模块，3：扩展模块，4：表单模块',
	PRIMARY KEY (`id`),
	KEY `title` (`title`),
	KEY `ext` (`ext`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='上传文件表';

#扩展模型表
DROP TABLE IF EXISTS `ph_expand`;
CREATE TABLE `ph_expand` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`table` varchar(50) UNIQUE NOT NULL DEFAULT '' COMMENT '模型表名称',
	`name` varchar(50) UNIQUE NOT NULL DEFAULT '' COMMENT '模型名称',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='扩展模型表';

#扩展模型字段表
DROP TABLE IF EXISTS `ph_expand_field`;
CREATE TABLE `ph_expand_field` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`expand_id` smallint UNSIGNED NOT NULL COMMENT 'expand表id',
	`name` varchar(50) NOT NULL COMMENT '字段描述',
	`field` varchar(50) NOT NULL COMMENT '字段名',
	`type` tinyint  NOT NULL DEFAULT '1' COMMENT '字段类型，1：文本框；2：多行文本；3：编辑器，4：文件上传；5：单图片上传；6：组图上传；7：下拉菜单；8：单选；9：多选',
	`property` tinyint  NOT NULL DEFAULT '1' COMMENT '字段属性，1：varchar；2：int；3：text；4：datetime；5：decimal；',
	`len` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT '长度',
	`decimal` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '小数点位数',
	`default` varchar(255) NOT NULL DEFAULT '' COMMENT '默认值',
	`sequence` smallint NOT NULL DEFAULT '0' COMMENT '排序，越小越排在前面',
	`tip` varchar(255) NOT NULL DEFAULT '' COMMENT '字段提示',
	`config` varchar(255) NOT NULL DEFAULT '' COMMENT '其他配置',
	`is_must` tinyint NOT NULL DEFAULT '0' COMMENT '是否必填，0：否，1：是',
	PRIMARY KEY (`id`),
	UNIQUE KEY `expand_id_name` (`expand_id`, `name`),
	UNIQUE KEY `expand_id_field` (`expand_id`, `field`),
	KEY `sequence` (`sequence`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='扩展模型字段';

#自定义变量表
DROP TABLE IF EXISTS `ph_fragment`;
CREATE TABLE `ph_fragment` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`sign` varchar(100) NOT NULL DEFAULT '' COMMENT '标识',
	`title` varchar(100) NOT NULL DEFAULT '' COMMENT '描述',
	`content` text COMMENT '内容',	
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`),
	KEY `sign` (`sign`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='自定义变量表';

#内容替换表
DROP TABLE IF EXISTS `ph_replace`;
CREATE TABLE `ph_replace` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`key` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
	`content` varchar(1000) NOT NULL DEFAULT '' COMMENT '要替换的内容',
	`num` smallint NOT NULL DEFAULT '0' COMMENT '替换次数，0：不限制',
	`status` tinyint NOT NULL DEFAULT '0' COMMENT '状态，0：禁用，1：启用',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`),
	KEY `language_id` (`language_id`, `status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='内容替换表';

#标签组表
DROP TABLE IF EXISTS `ph_tags_group`;
CREATE TABLE `ph_tags_group` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '' COMMENT '标签组名',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='标签组表';

#标签表
DROP TABLE IF EXISTS `ph_tags`;
CREATE TABLE `ph_tags` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`tags_group_id` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'tags_group表id',
	`name` varchar(100) NOT NULL DEFAULT '' COMMENT '标签名',
	`click` int UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击次数',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`),
	KEY `tags_group_id` (`tags_group_id`),
	KEY `name` (`name`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='标签表';

#内容标签关系表
DROP TABLE IF EXISTS `ph_tags_relation`;
CREATE TABLE `ph_tags_relation` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_content_id` int UNSIGNED NOT NULL COMMENT 'category_content表id',
	`tags_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'tags表id',
	PRIMARY KEY (`id`),
	KEY `category_content_id` (`category_content_id`),
	KEY `tags_id` (`tags_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='内容标签关系表';

#推荐位表
DROP TABLE IF EXISTS `ph_position`;
CREATE TABLE `ph_position` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
	`sequence` smallint NOT NULL DEFAULT '0' COMMENT '排序，升序',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `sequence` (`sequence`),
	KEY `language_id` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='推荐位表';

#推荐位与内容关系表
DROP TABLE IF EXISTS `ph_category_content_position`;
CREATE TABLE `ph_category_content_position` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_content_id` int UNSIGNED NOT NULL COMMENT 'category_content表id',
	`position_id` int UNSIGNED NOT NULL COMMENT 'position表id',
	PRIMARY KEY (`id`),
	KEY `category_content_id` (`category_content_id`),
	KEY `position_id` (`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='推荐位与内容关系表';

#模型表
DROP TABLE IF EXISTS `ph_category_model`;
CREATE TABLE `ph_category_model` (
	`id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL COMMENT '模型名称',
	`category` varchar(50) NOT NULL DEFAULT '' COMMENT '栏目控制器名',
	`content` varchar(50) NOT NULL DEFAULT '' COMMENT '内容控制器名',
	`status` tinyint NOT NULL DEFAULT '0' COMMENT '状态，0：禁用，1：开启',
	`befrom` varchar(255) NOT NULL DEFAULT '' COMMENT '来源',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='模型表';

TRUNCATE `ph_category_model`;
INSERT INTO `ph_category_model` VALUES 
(1, '新闻', 'categorynews', 'categorycontent', 1, 'phalconcms'),
(2, '页面', 'categorypage', '', 1, ''),
(3, '跳转', 'categoryjump', '', 1, '');

#栏目表
DROP TABLE IF EXISTS `ph_category`;
CREATE TABLE `ph_category` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`pid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT '上级栏目id',
	`category_model_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'category_model表id',
	`sequence` smallint NOT NULL DEFAULT '0' COMMENT '排序，越小越排在前面',
	`is_show` tinyint NOT NULL DEFAULT '1' COMMENT '是否显示，1：显示，0：隐藏',
	`type` tinyint NOT NULL DEFAULT '1' COMMENT '栏目类型，1：频道页，2：列表页',
	`name` varchar(100) NOT NULL COMMENT '栏目名称',
	`urlname` varchar(255) UNIQUE NOT NULL COMMENT '栏目url优化',
	`subname` varchar(100) NOT NULL DEFAULT '' COMMENT '副栏目名称',
	`image` varchar(255) NOT NULL DEFAULT '' COMMENT '栏目形象图',
	`category_tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '栏目模板',
	`content_tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '内容模板',
	`page` tinyint UNSIGNED NOT NULL DEFAULT '10' COMMENT '内容分页数',
	`keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词，","分割',
	`description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
	`seo_content` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO内容',
	`content_order` varchar(255) NOT NULL DEFAULT '' COMMENT '内容排序，"updatetime DESC"：更新时间 新旧，"updatetime ASC"：更新时间 旧新，"inputtime DESC"：发布时间 新旧，"inputtime ASC"：发布时间 旧新，"sequence DESC"：自定义顺序 大小，"sequence ASC"：自定义顺序 小大',
	`language_id` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'language表id',
	`expand_id` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT '扩展表id',
	PRIMARY KEY (`id`),
	KEY `pid` (`pid`),
	KEY `category_model_id` (`category_model_id`),
	KEY `name` (`name`),
	KEY `urlname2` (`urlname`, `is_show`),
	KEY `sequence` (`sequence`),
	KEY `language_id` (`language_id`),
	KEY `expand_id` (`expand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='栏目表';

#栏目内容表
DROP TABLE IF EXISTS `ph_category_content`;
CREATE TABLE `ph_category_content` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'category表id',
	`title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
	`urltitle` varchar(100) NOT NULL DEFAULT '' COMMENT 'URL路径',
	`subtitle` varchar(100) NOT NULL DEFAULT '' COMMENT '短标题',
	`font_color` char(7) NOT NULL DEFAULT '' COMMENT '颜色(16进制RGB值)',
	`font_bold` tinyint NOT NULL DEFAULT '0' COMMENT '加粗，0：不加粗，1：加粗',
	`keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词',
	`description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
	`updatetime` datetime NOT NULL COMMENT '更新时间',
	`inputtime` datetime DEFAULT NULL COMMENT '发布时间',
	`image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
	`url` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转',
	`sequence` smallint NOT NULL DEFAULT '0' COMMENT '排序',
	`tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '模板',
	`status` tinyint NOT NULL DEFAULT '0' COMMENT '状态，0：草稿，1：发布',
	`copyfrom` varchar(255) NOT NULL DEFAULT '' COMMENT '来源',
	`views` int NOT NULL DEFAULT '0' COMMENT '浏览数',
	`position` varchar(255) NOT NULL DEFAULT '0' COMMENT '推荐ids',
	`taglink` tinyint NOT NULL DEFAULT '0' COMMENT '是否内容自动TAG',
	PRIMARY KEY (`id`),
	KEY `category_id` (`category_id`, `status`),
	KEY `title` (`title`),
	KEY `urltitle` (`urltitle`, `status`),
	KEY `updatetime` (`updatetime`),
	KEY `inputtime` (`inputtime`),
	KEY `views` (`views`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='内容表';

#内容数据表
DROP TABLE IF EXISTS `ph_category_content_data`;
CREATE TABLE `ph_category_content_data` (
	`id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_content_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'category_content表id',
	`content` text,
	PRIMARY KEY (`id`),
	KEY `category_content_id` (`category_content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='内容数据表';

#跳转栏目附加表
DROP TABLE IF EXISTS `ph_category_jump`;
CREATE TABLE `ph_category_jump` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_id` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'category表id',
	`url` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转地址',
	PRIMARY KEY (`id`),
	KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='跳转栏目附加表';

#页面栏目附加表
DROP TABLE IF EXISTS `ph_category_page`;
CREATE TABLE `ph_category_page` (
	`id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_id` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'category表id',
	`content` text COMMENT '内容',
	PRIMARY KEY (`id`),
	KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='页面栏目附加表';

