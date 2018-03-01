<?php
class CookieController extends ModelBase{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }
    public function indexAction(){
        $cookie = $this->request->getPost('mycookie','string','');
        $user = json_decode(base64_decode($cookie),true);
        if(!empty($user)){
            $user = ExUsers::itemById($user['id']);
            if(empty($user)){
                setcookie('EXCHANGE',base64_encode(json_encode($user)),time()+60*60,'/','xinhuodata.com');
            }
        }
    }
}