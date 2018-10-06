// 获取查询字符串的值
function getUrlParam(name) {
	var AllVars = window.location.search.substring(1);
    var Vars = AllVars.split("&");
    for (i = 0; i < Vars.length; i++) {
        var Var = Vars[i].split("=");
        if (Var[0] == name) return Var[1];
    }
    return "";
}

// JS 去除两端空格
function trim(str) {
	return str.replace(/(^\s*)|(\s*$)/g,"");
}

// 返回当前时间，用于赋值datetime类型，返回格式为 201508181758
function getDate() {
	var d = new Date();
	var vYear = d.getFullYear();
	var vMon = d.getMonth() + 1;
	var vDay = d.getDate();
	var h = d.getHours(); 
	var m = d.getMinutes(); 
	var se = d.getSeconds();
	s = vYear + "" +
		+ (vMon<10 ? "0" + vMon : vMon)
		+ (vDay < 10 ? "0" + vDay : vDay)
		+ (h < 10 ? "0" + h : h)
		+ (m < 10 ? "0" + m : m)
		+ (se < 10 ? "0" + se : se);
	return s;
}

// 验证手机号码
function telval(tel) {
	var myreg = /^0?1[3|4|5|7|8][0-9]\d{8}$/; 
    return myreg.test(tel) ? true : false;
}