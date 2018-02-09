$("#sendAgain").click(function(){
    $.post('/user/sendMail',{d:$("#d").val()},function(json){
        if(json.code == '0000'){
            window.location.href = '/user/sendmail?d='+json.data;
        }else{
            errMsg('邮件发送失败，请重试');
        }
    })
});