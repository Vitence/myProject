<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {

    public $userInfo = [];
    
    public function initialize() {
        $this->userInit();
    }
    
    /**
     * 页面跳转
     * @param $url
     */
    public function redirect($url) {
        $response = new \Phalcon\Http\Response();
        $response->redirect($url);
        $response->send();
    }
    
    /**
     * debug
     * @return bool
     */
    public function isDebug() {
        return $this->debugConfig['debug']['isDebug'] || false;
    }
    
    
    /**
     * 初始化用户信息
     */
    public function userInit(){
        $userInfo = $this->session->get('userInfo');
        if ($userInfo){
            $this->userInfo = $userInfo;
        }
        $this->view->setVar('userInfo',$this->userInfo);
    }
    
    
    /**
     * 数据返回
     * @param        $results
     * @param string $errorCode
     * @param string $errorMessage
     */
    protected function jsonReturn($results, $errorCode = '0000', $errorMessage = '成功') {
        $this->response = array(
            'code' => $errorCode,
            'msg'  => $errorMessage,
            'data' => $results,
            'time' => date('Y-m-d H:i:s',time())
        );
        header('Content-type:text/json;charset=utf-8');
        echo json_encode($this->response,JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * 404页面（？）
     */
    public function _404(){
        self::redirect('/');
    }
    
    public function saveSession($user){
        unset($user['password']);
        unset($user['trading_password']);
        $this->session->set('userInfo',$user);
    }
    
    public function deleteSession(){
        $this->session->destroy();
    }
}
