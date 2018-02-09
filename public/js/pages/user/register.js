$(document).delegate('.imgCode', 'click', function(){
    var self = $(this);
    $.get('/user/getImgCode',{},function(json){
        if(json.code == '0000'){
            self.attr("src",json.data);
        }else{
            errMsg('获取图片验证码错误,请刷新页面重试！');
        }
    })
});

/**
 * 注册
 */
$(document).delegate('#register','click',function(){
    var self = $(this);

    var emailEle         = $("#email");
    var passwordEle      = $("#password");
    var passwordAgainEle = $("#passwordAgain");
    var imgCodeEle       = $("#imgCode");

    var clause        = $("#clause").val().trim();
    var account       = emailEle.val().trim();
    var imgCode       = imgCodeEle.val().trim();
    var password      = passwordEle.val().trim();
    var passwordAgain = passwordAgainEle.val().trim();
    if(clause !== 'on'){
        errMsg('请先阅读条款');return;
    }

    if (!checkEmail(account)){
        userError(emailEle,'邮箱格式错误');return;
    }

    if(!checkPassword(password)){
        userError(passwordEle,'请填写8-16位字母加数字密码');return;
    }

    if(passwordAgain !== password){
        userError(passwordEle,'两次密码输入不一样');return;
    }

    if(imgCode == ''){
        userError(imgCodeEle,'请输入图片验证码');return;
    }

    disableButton(self);

    var tokenName = $("input[name='tokenName']").val();
    var token = $("input[name='token']").val();

    $.post('/user/register',{
        account:account,
        imgCode:imgCode,
        password:password,
        tokenName:tokenName,
        token:token
    },function(json){
        enableButton(self);
        $("input[name='tokenName']").val(json.data.tokenName);
        $("input[name='token']").val(json.data.token);
        if (json.code == '0000'){
            window.location.href='/user/registerSuccess';
        }else if(json.code == 1001){
            userError(imgCodeEle,'图片验证码错误');
        }else if(json.code == 1002){
            userError(emailEle,'邮箱格式错误');
        }else if(json.code == 1003){
            userError(passwordEle,'请填写8-16位字母加数字密码');
        }else if(json.code == 1004){
            userError(emailEle,'邮箱已被注册');
        }else{
            successMsg('注册失败，请重试');
        }
    });
});