$("#reset").click(function(){
    var accountEle = $(".forgetPassBlock").find('input').eq(0);
    var imgCodeEle = $(".forgetPassBlock").find('input').eq(1);
    var tokenName = $("input[name='tokenName']").val();
    var token = $("input[name='token']").val();
    if(accountEle.val().trim() == ""){
        userError(accountEle,'请输入邮箱');return;
    }
    if(imgCodeEle.val().trim() == ""){
        userError(imgCodeEle,'请输入图片验证码');return;
    }
    $.post('/password/forget',{
        account:accountEle.val().trim(),
        imgCode:imgCodeEle.val().trim(),
        tokenName:tokenName,
        token:token
    },function(json){
        $("input[name='tokenName']").val(json.data.tokenName);
        $("input[name='token']").val(json.data.token);
        if(json.code =='0000'){
            successMsg('邮件发送成功，请从邮件点击链接修改密码');
        }else if(json.code =='1001'){
            userError(imgCodeEle,'图片验证码错误');return;
        }else if(json.code == '1004'){
            userError(accountEle,'邮箱未注册');return;
        }else if(json.code == '1002'){
            userError(accountEle,'邮箱格式错误');return;
        }else{
            errMsg('邮件发送失败');
        }
    })
});

$("#set").click(function(){
    var passwordEle = $(".resetPassBlock").find('input').eq(0);
    var passwordAgainEle = $(".resetPassBlock").find('input').eq(1);
    var tokenName = $("input[name='tokenName']").val();
    var token = $("input[name='token']").val();
    if(passwordEle.val().trim() == ""){
        userError(passwordEle,'请输入密码');return;
    }
    if(!checkPassword(passwordEle.val().trim())){
        userError(passwordEle,'请填写8-16位字母加数字密码');return;
    }

    if(passwordEle.val().trim() != passwordAgainEle.val().trim()){
        userError(passwordAgainEle,'两次密码输入不同');return;
    }
    $.post('/password/set',{
        password:passwordEle.val().trim(),
        d:$("input[name='data']").val(),
        tokenName:tokenName,
        token:token
    },function(json){
        $("input[name='tokenName']").val(json.data.tokenName);
        $("input[name='token']").val(json.data.token);
        if(json.code =='0000'){
            window.location.href='/password/success';
        }else{
            errMsg('密码修改失败');
        }
    })
});