<?php
use Util\common;
use Util\Check;
class UserController extends ControllerBase{

    public function initialize(){
        parent::initialize();
    }
    
    public function indexAction(){
    
    }
    
    /**
     * 登陆
     */
    public function loginAction(){
        if(!empty($this->userInfo)){
            self::redirect('/');
        }
        if($this->request->isPost()){
            $token     = $this->request->getPost('token','string','');
            $tokenName = $this->request->getPost('tokenName','string','');
            $account   = $this->request->getPost('account','string','');
            $password  = $this->request->getPost('password','string','');
        
        
           
            if (!$this->security->checkToken($tokenName,$token)){
                $this->jsonReturn('','1000','页面超时，请刷新页面重试');
            }
    
            $dataToken['tokenName']  = $this->security->getTokenKey();
            $dataToken['token']      = $this->security->getToken();
            if (!Check::VerifyEmail($account)){
                $this->jsonReturn($dataToken,'1002','邮箱格式错误');
            }
            
            $user = ExUsers::itemByEmail($account);
            
            if(empty($user)){
                $this->jsonReturn($dataToken,'1005','邮箱未注册');
            }
            
            if($user['status'] == ExUsers::LOCK){
                $this->jsonReturn($dataToken,'1004','账户已被锁定');
            }
            if($user['password'] == md5(md5($password))){
                ExUsers::saveLastLogin($user['id']); //记录登陆时间
                self::saveSession($user);
                $this->jsonReturn($dataToken,'0000','登录成功');
            }else{
                $this->jsonReturn($dataToken,'100','登录失败');
            }
        }
    }
    
    /**
     * 注册提交
     */
    public function registerAction(){
        if($this->request->isPost()){
            $token     = $this->request->getPost('token','string','');
            $tokenName = $this->request->getPost('tokenName','string','');
            $account   = $this->request->getPost('account','string','');
            $password  = $this->request->getPost('password','string','');
            $imgCode   = $this->request->getPost('imgCode','string','');
            
            if (!$this->security->checkToken($tokenName,$token)){
                $this->jsonReturn('','1000','页面超时，请刷新页面重试');
            }
    
            $dataToken['tokenName']  = $this->security->getTokenKey();
            $dataToken['token']      = $this->security->getToken();
            
            if (!Check::VerifyEmail($account)){
                $this->jsonReturn($dataToken,'1002','邮箱格式错误');
            }
    
            $user = ExUsers::itemByEmail($account);
            if($user){
                $this->jsonReturn($dataToken,'1004','邮箱已被注册');
            }
            
            if (!Check::verifyPassword($password)){
                $this->jsonReturn($dataToken,'1003','密码格式错误');
            }
    
            if (strtolower($imgCode) != $this->session->get('imgCode')){
                $this->jsonReturn($dataToken,'1001','图片验证码错误');
            }
            
            $data['email'] = $account;
            $data['password'] = md5(md5($password));
//            $data['expire']  = time() + (24 * 3600);
//            $data['type']  = EmailLogic::REGISTER;
    
            $add = ExUsers::addUser($data);


//            $send = EmailLogic::sendMail($data);
//            $dataToken = $data;
//            $dataToken['d'] = base64_encode(json_encode($data));
            
            if ($add){
                $this->jsonReturn($dataToken);
            } else{
                $this->jsonReturn($dataToken,'100','注册失败');
            }
        }
        $imgCode = common::getCaptcha();
        $this->session->set('imgCode',$imgCode['code']);
        $this->view->setVar('codeUrl',$imgCode['codeUrl']);
    }
    
    public function getImgCodeAction(){
        //次数验证
        $imgCode = common::getCaptcha();
        $this->session->set('imgCode',$imgCode['code']);
        $this->jsonReturn($imgCode['codeUrl']);
    }
    
    /**
     * 邮件激活页面
     */
//    public function activeAction(){
//        $data = $this->request->getQuery('d','string','');
//        $code = '0000';
//        if($data){
//            $data = json_decode(base64_decode($data),true);
//            if($data['sign'] != EmailLogic::SIGN){
//                $this->_404();
//            }
//
//            if($data['expire'] < time()){
//                $code = '1100';
//            }
//
//            $saveData['email'] = $data['email'];
//            $saveData['password'] = $data['password'];
//            $saveData['register_at'] = common::getDataTime();
//            $saveData['last_login_at'] = common::getDataTime();
//
//            $saveUser = ExUsers::addData(new ExUsers(),$saveData);
//
//            if(!$saveUser){
//                $code = '100';
//            }
//            $this->view->setVar('code',$code);
//        }else{
//            $this->_404();
//        }
//    }
    
    
    public function registerSuccessAction(){
    
    }

    public function logoutAction(){
        self::deleteSession();
        self::redirect('/user/login');
    }
}