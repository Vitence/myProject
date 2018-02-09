<?php
class MailController extends ControllerBase{
    
    /**
     * 注册完后的发送邮件页面
     */
    public function sendMailAction(){
        $data = $this->request->getQuery('d','string','');
        if($this->request->isPost()){
            $data = $this->request->getPost('d','string','');
            $data = json_decode(base64_decode($data),true);
            
            $data['expire'] = time() + (24 * 3600);
            $send = EmailLogic::sendMail($data['type']);
            
            if ($send){
                $this->jsonReturn(base64_encode(json_encode($data)));
            } else{
                $this->jsonReturn('','100','邮件发送失败');
            }
        }
        if(!$data){
            $this->_404();
        }
    }
}