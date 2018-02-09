$(document).delegate('#edit ','click',function(){
    var self = $(this);

    var oldPasswrodEle      = $("#oldPassword");
    var passwordEle      = $("#password");
    var passwordAgainEle = $("#passwordAgain");

    var oldPasswrod   = oldPasswrodEle.val();
    var password      = passwordEle.val().trim();
    var passwordAgain = passwordAgainEle.val().trim();

    var data = {};

    if(typeof(oldPasswrod) != 'undefined'){
        oldPasswrod = oldPasswrod.trim();
        data.oldPass = oldPasswrod;
    }

    if(!checkPassword(password)){
        userError(passwordEle,'请填写8-16位字母加数字密码');return;
    }

    if(passwordAgain !== password){
        userError(passwordEle,'两次密码输入不一样');return;
    }

    disableButton(self);

    var tokenName = $("input[name='tokenName']").val();
    var token = $("input[name='token']").val();

    data.password = password;
    data.tokenName = tokenName;
    data.token = token;

    $.post('/security/setTradingPass',data,function(json){
        enableButton(self);
        $("input[name='tokenName']").val(json.data.tokenName);
        $("input[name='token']").val(json.data.token);
        if (json.code == '0000'){
            $(".edit").hide();
            $(".editSuccess").show();
        }else if(json.code == 1007){
            userError(oldPasswrodEle,'原始登陆密码错误');
        }else if(json.code == 1003) {
            userError(passwordEle, '请填写8-16位字母加数字密码');
        }else{
            successMsg('密码修改失败，请刷新页面重试');
        }
    });
});