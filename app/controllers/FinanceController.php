<?php
class FinanceController extends ControllerBase{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        if(empty($this->userInfo)){
            self::redirect('/user/login');
        }
    }
    
    public function indexAction(){
        $user = $this->userInfo;
        $items = ExExchangeRecord::itemsByUserId($user['id']);
        $currencyId = array_column($items,'currency_id');
        $types = ExCurrency::itemsByIdArr($currencyId);
        $types = array_column($types,null,'id');
        $user = ExUsers::itemById($user['id']);
        foreach ($items as &$item){
            $item['name'] = $types[$item['currency_id']]['name'];
            $newPrice = ExOrder::getMaxOrderPrice($item['currency_id']);
            if($newPrice){
                $newPrice = $newPrice->toArray();
                if(!empty($newPrice)){
                    $item['newPrice'] = $newPrice['price'];
                }else{
                    $item['newPrice'] = 0;
                }
            }else{
                $item['newPrice'] = 0;
            }
        }
        foreach ($items as &$val){
            if($val['newPrice'] <= 0){
                $val['newPrice'] = $types[$val['currency_id']]['init_price'];
            }
        }
        $this->view->setVar('items',$items);
        $this->view->setVar('user',$user);
    }
    
    
    public function capitalRecordAction(){
    }
    
    public function getMyOrderAction(){
        $user = $this->userInfo;
        $items = ExOrder::itemsByUserId($user['id']);
        $currencyId = array_column($items,'currency_id');
        $types = ExCurrency::itemsByIdArr($currencyId);
        $types = array_column($types,null,'id');
        foreach ($items as &$item){
            $item['name'] = $types[$item['currency_id']]['name'];
        }
        $this->jsonReturn($items);
    }
}