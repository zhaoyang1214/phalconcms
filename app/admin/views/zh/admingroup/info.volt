<link href="{{ static_url('ztree/css/zTreeStyle.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_url('ztree/jquery.ztree.js') }}"></script>
<script src="{{ static_url('ztree/jquery.ztree.exhide.js') }}"></script>
<script src="{{ static_url('ztree/jquery.ztree.excheck.js') }}"></script>
<script>

var cateGorySetting = {
    view: {
		nameIsHTML: true
    },
	check: {
            enable: true
    },
    data: {
		key: {
			title:"name"
		},
        simpleData: {
            enable: true,
            idKey: "id",
            pIdKey: "pid",
            rootPId: 0
        }
    }
};
var cateGoryNodes = {{ cateGoryTree }};

var formSetting = {
    view: {
		nameIsHTML: true
    },
	check: {
            enable: true
    },
    data: {
		key: {
			title:"name"
		},
		
        simpleData: {
            enable: true,
            idKey: "id",
            pIdKey: "",
            rootPId: ""
        }
    }
};
var formNodes = {{ formTree }};


var adminAuthSetting = {
    view: {
		nameIsHTML: true
    },
	check: {
            enable: true
    },
    data: {
		key: {
			title:"note"
		},
		
        simpleData: {
            enable: true,
            idKey: "id",
            pIdKey: "pid",
            rootPId: 0
        }
    }
};
var adminAuthNodes = {{ adminAuthTree }};

$(document).ready(function() {
    $.fn.zTree.init($("#cateGoryTree"), cateGorySetting, cateGoryNodes);
    $.fn.zTree.init($("#formTree"), formSetting, formNodes);
    $.fn.zTree.init($("#adminAuthTree"), adminAuthSetting, adminAuthNodes);
});
</script>

<div class="page_function">
  <div class="info">
    <h3>管理组{{ actionName }}</h3>
    <small>使用以下功能进行管理组{{ actionName }}操作</small> 
  </div>
</div>
<div class="tab" id="tab"> 
<a class="selected" href="#tab1">基本信息</a>
<a href="#tab4" id="akeep4" {% if adminGroup.keep is defined and bitwise(adminGroup.keep, '&', 4) %}style="display:none;"{% endif %}>功能操作权限</a>
<a href="#tab2" id="akeep2" {% if adminGroup.keep is defined and bitwise(adminGroup.keep, '&', 2) %}style="display:none;"{% endif %}>栏目内容权限</a>
<a href="#tab3" id="akeep1" {% if adminGroup.keep is defined and bitwise(adminGroup.keep, '&', 1) %}style="display:none;"{% endif %}>多功能表单权限</a>
{% if admingroupIndexPower %}
 <a  href="javascript:menuload('{{ url('Admingroup/index') }}')">返回管理组列表</a>
{% endif %}
 </div>
<div class="page_form">
<form action="{{ actionUrl }}" method="post" id="form">
<div class="page_table form_table" id="tab1">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td width="100" align="right">管理组名称</td>
          <td width="450"><input name="name" type="text" class="text_value" id="name" value="{% if adminGroup.name is defined %}{{ adminGroup.name }}{% endif %}" reg="\S" msg="管理组名称不能为空" /></td>
          <td></td>
        </tr>
        <tr>
          <td width="100" align="right">管理等级</td>
          <td width="450">
          <input name="grade" type="text" class="text_value" id="grade" placeholder="请填写 {{ adminGroupInfo['grade'] + 1 }} - 99 之间的整数" value="{% if adminGroup.grade is defined %}{{ adminGroup.grade }}{% endif %}" reg="\S" msg="管理等级不能为空" />
          </td>
          <td>请填写 {{ adminGroupInfo['grade'] + 1 }} - 99 之间的整数</td>
        </tr>
        {% if adminGroupInfo['keep'] %}
        <tr>
          <td width="100" align="right">选择操作权限</td>
          <td width="450">
          {% if bitwise(adminGroupInfo['keep'], '&', 4) %}
          <input name="keep[]" type="checkbox" value="4" class="keep" id="keep4" {% if adminGroup.keep is defined and bitwise(adminGroup.keep, '&', 4) %}checked{% endif %}>
          <label for="keep4">允许操作所有功能</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['keep'], '&', 2) %}
          <input name="keep[]" type="checkbox" value="2"  class="keep" id="keep2" {% if adminGroup.keep is defined and bitwise(adminGroup.keep, '&', 2) %}checked{% endif %}>
          <label for="keep2">允许操作所有栏目</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['keep'], '&', 1) %}
          <input name="keep[]" type="checkbox" value="1"  class="keep" id="keep1" {% if adminGroup.keep is defined and bitwise(adminGroup.keep, '&', 1) %}checked{% endif %}>
          <label for="keep1">允许操作所有多功能表单</label>&nbsp;&nbsp;
          {% endif %}
          </td>
          <td>若不选择，系统会根据“功能操作权限”、“栏目操作权限”、“多功能表单操作权限”所勾选的权限进行访问限制</td>
        </tr>
        {% endif %}
        {% if adminGroupInfo['group_power'] %}
        <tr>
          <td width="100" align="right">管理组列表权限</td>
          <td width="450">
          {% if bitwise(adminGroupInfo['group_power'], '&', 1) %}
          <input name="group_power[]" type="checkbox" value="1" id="group_power1" {% if adminGroup.group_power is defined and bitwise(adminGroup.group_power, '&', 1) %}checked{% endif %}>
          <label for="group_power1">显示本组</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['group_power'], '&', 2) %}
          <input name="group_power[]" type="checkbox" value="2" id="group_power2" {% if adminGroup.group_power is defined and bitwise(adminGroup.group_power, '&', 2) %}checked{% endif %}>
          <label for="group_power2">显示同级别组</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['group_power'], '&', 4) %}
          <input name="group_power[]" type="checkbox" value="4" id="group_power4" {% if adminGroup.group_power is defined and bitwise(adminGroup.group_power, '&', 4) %}checked{% endif %}>
          <label for="group_power4">显示低级别组</label>&nbsp;&nbsp;
          {% endif %}
          </td>
          <td>针对管理组列表显示（功能操作权限也需要设置相应的权限）</td>
        </tr>
        {% endif %}
        {% if adminGroupInfo['admin_power'] %}
        <tr>
          <td width="100" align="right">管理员列表权限</td>
          <td width="450">
          {% if bitwise(adminGroupInfo['admin_power'], '&', 1) %}
          <input name="admin_power[]" type="checkbox" value="1" id="admin_power1" {% if adminGroup.admin_power is defined and bitwise(adminGroup.admin_power, '&', 1) %}checked{% endif %}>
          <label for="admin_power1">显示管理员本人</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['admin_power'], '&', 2) %}
          <input name="admin_power[]" type="checkbox" value="2" id="admin_power2" {% if adminGroup.admin_power is defined and bitwise(adminGroup.admin_power, '&', 2) %}checked{% endif %}>
          <label for="admin_power2">显示同级别组管理员</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['admin_power'], '&', 4) %}
          <input name="admin_power[]" type="checkbox" value="4" id="admin_power4" {% if adminGroup.admin_power is defined and bitwise(adminGroup.admin_power, '&', 4) %}checked{% endif %}>
          <label for="admin_power4">显示低级别组管理员</label>
          {% endif %}
          <br/>
          <br/>
          {% if bitwise(adminGroupInfo['admin_power'], '&', 8) %}
          <input name="admin_power[]" type="checkbox" value="8" id="admin_power8" {% if adminGroup.admin_power is defined and bitwise(adminGroup.admin_power, '&', 8) %}checked{% endif %}>
          <label for="admin_power8">修改管理员本人</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['admin_power'], '&', 16) %}
          <input name="admin_power[]" type="checkbox" value="16" id="admin_power16" {% if adminGroup.admin_power is defined and bitwise(adminGroup.admin_power, '&', 16) %}checked{% endif %}>
          <label for="admin_power16">修改同级别组管理员</label>&nbsp;&nbsp;
          {% endif %}
          {% if bitwise(adminGroupInfo['admin_power'], '&', 32) %}
          <input name="admin_power[]" type="checkbox" value="32" id="admin_power32" {% if adminGroup.admin_power is defined and bitwise(adminGroup.admin_power, '&', 32) %}checked{% endif %}>
          <label for="admin_power32">修改低级别组管理员</label>
          {% endif %}
          </td>
          <td>针对管理员权限（功能操作权限也需要设置相应的权限）,这里的修改指的是修改资料</td>
        </tr>
        {% endif %}
        {% if !adminGroupInfo['language_power'] %}
        <tr>
          <td width="100" align="right">是否受语言限制</td>
          <td width="450">
          <input type="radio" name="language_power" id="language_power1" value="1" {% if action=='add' or (adminGroup.admin_power is defined and adminGroup.language_power) %}checked{% endif %}/>
          <label for="language_power1">是</label>&nbsp;&nbsp;
          <input type="radio" name="language_power" id="language_power0" value="0" {% if adminGroup.admin_power is defined and !adminGroup.language_power %}checked{% endif %}/>
          <label for="language_power0">否</label>&nbsp;&nbsp;
          </td>
          <td>不受语言限制会允许增、删、改、查他国语言管理组和管理员（开启多国语言有效）</td>
        </tr>
        {% if config.system.language_status %}
        <tr>
          <td width="100" align="right">管理组所属语言</td>
          <td width="450">
          <select name="language_id">
			{% for language in languageList %}
			<option value="{{ language.id }}" {% if adminGroup.admin_power is defined and adminGroup.language_id==language.id %}selected{% endif %}>{{ language.lang }}（{{ language.zh_name }}）</option>
			{% endfor %}
		  </select> 
          </td>
          <td></td>
        </tr>
        {% endif %}
        {% endif %}
    </table>
</div>

<div class="page_table form_table" id="tab2" style="display: none;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" align="right">栏目操作权限
          <br />
			</td>
          <td colspan="2">
            <ul id="cateGoryTree" class="ztree">
            </ul>
          </td>
        </tr>
        
    </table>
</div>
<div class="page_table form_table" id="tab3" style="display: none;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" align="right">多功能表单操作权限
          <br />
			</td>
          <td colspan="2">
            <ul id="formTree" class="ztree">
            </ul>
          </td>
        </tr>
        
    </table>
</div>
<div class="page_table form_table" id="tab4" style="display: none;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" align="right">功能操作权限
          <br />
			</td>
          <td colspan="2">
            <ul id="adminAuthTree" class="ztree">
            </ul>
          </td>
        </tr>
        
    </table>
</div>

{% if actionPower %}
<!--普通提交-->
<div class="form_submit">
{% if action=='edit' %}
<input name="id" type="hidden" value="{{ adminGroup.id }}" />
{% endif %}
<input name="admin_auth_ids" id="admin_auth_ids" type="hidden" value="{% if adminGroup.admin_auth_ids is defined %}{{adminGroup.admin_auth_ids}}{% endif %}" />
<input name="category_ids" id="category_ids" type="hidden" value="{% if adminGroup.category_ids is defined %}{{adminGroup.category_ids}}{% endif %}" />
<input name="form_ids" id="form_ids" type="hidden" value="{% if adminGroup.form_ids is defined %}{{adminGroup.form_ids}}{% endif %}" />
<button type="submit" click_tip="{{ actionName }}中..." class="button">{{ actionName }}</button> 
</div>
{% endif %}
</form>
</div>
</div>
<script type="text/javascript">
//tab菜单
$("#tab").idTabs();

$('.keep').click(function() {
	var keepId = '#akeep' + $(this).val();
	var obj = $(keepId);
	if($(this).is(":checked")){
		obj.hide();
	}else{
		obj.show();
	}
});
//提交表单
savelistform({
	//debug: true,
	addurl:"{{ request.getURI() }}",
	listurl:"{{ url('Admingroup/index') }}",
	name : '{{ jumpButton }}',
	data:function(){
		var cateGoryTreeObj = $.fn.zTree.getZTreeObj("cateGoryTree");
		var cateGoryCheckedNodes = cateGoryTreeObj.getCheckedNodes(true);
		var cateGoryPurview = "";
		for (var i = 0; i < cateGoryCheckedNodes.length; i++) {
			cateGoryPurview +=  cateGoryCheckedNodes[i].id+",";
	    }
		$('#category_ids').val(cateGoryPurview);
		
		var formTreeObj = $.fn.zTree.getZTreeObj("formTree");
		var formCheckedNodes = formTreeObj.getCheckedNodes(true);
		var formPurview = "";
		for (var i = 0; i < formCheckedNodes.length; i++) {
			formPurview +=  formCheckedNodes[i].id+",";
	    }
		$('#form_ids').val(formPurview);
		
		
		var adminAuthTreeObj = $.fn.zTree.getZTreeObj("adminAuthTree");
		var adminAuthCheckedNodes = adminAuthTreeObj.getCheckedNodes(true);
		var adminAuthPurview = "";
		for (var i = 0; i < adminAuthCheckedNodes.length; i++) {
			adminAuthPurview +=  adminAuthCheckedNodes[i].id+",";
	    }
		$('#admin_auth_ids').val(adminAuthPurview);
		return true;
	}
});
</script>