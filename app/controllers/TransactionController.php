<?php
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
class TransactionController extends ControllerBase{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
//        if(empty($this->userInfo)){
//            self::redirect('/user/login');
//        }
    }
    
    public function indexAction(){
        $items = ExCurrency::find();
        $items = $items->toArray();
        $this->view->setVar('items',$items);
    }
    
    public function buyAction(){
        if($this->request->isPost()){
            $token     = $this->request->getPost('token','string','');
            $tokenName = $this->request->getPost('tokenName','string','');
            if (!$this->security->checkToken($tokenName,$token)){
                $this->jsonReturn('','1000','页面超时，请刷新页面重试');
            }
//
            $dataToken['tokenName']  = $this->security->getTokenKey();
            $dataToken['token']      = $this->security->getToken();
    
            $number  = $this->request->getPost('number','int',0);
            $price   = $this->request->getPost('price','float',0);
            $type   = $this->request->getPost('type','int',0);
            $password   = $this->request->getPost('password','string','');
            if($number <= 0 || $price <= 0 || $type <= 0 || !in_array($type,[1,2,3,4])){
                $this->jsonReturn($dataToken,'1000','页面超时，请刷新页面重试');
            }
            
            //大于1点 小于8点 未未开盘
            if(date("H") < 8 || date("H") > 19){
                $this->jsonReturn($dataToken,'1030','闭盘时间');
            }       
            $user = $this->userInfo;
            if(empty($user)){
                $this->jsonReturn($dataToken,'1020','未登录');
            }
            $user = ExUsers::itemById($user['id']);
            
            if($user['trading_password'] != md5(md5($password))){
                $this->jsonReturn($dataToken,'1011','交易密码错误');
            }
            
            if($user['balance'] < $number * $price){
                $this->jsonReturn($dataToken,'1010','余额不足');
            }
            $save = OrderLogic::saveTransaction($number,$price,$type,1,$user['id']);
            $this->jsonReturn($dataToken);
        }
    }
    
    public function saleAction(){
        if($this->request->isPost()){
            $token     = $this->request->getPost('token','string','');
            $tokenName = $this->request->getPost('tokenName','string','');
            if (!$this->security->checkToken($tokenName,$token)){
                $this->jsonReturn('','1000','页面超时，请刷新页面重试');
            }
//
            $dataToken['tokenName']  = $this->security->getTokenKey();
            $dataToken['token']      = $this->security->getToken();
            
            $number  = $this->request->getPost('number','int',0);
            $price   = $this->request->getPost('price','float',0);
            $type   = $this->request->getPost('type','int',0);
            $password   = $this->request->getPost('password','string','');
            if($number <= 0 || $price <= 0 || $type <= 0 || !in_array($type,[1,2,3,4])){
                $this->jsonReturn($dataToken,'1000','页面超时，请刷新页面重试');
            }
            if(date("H") < 8 || date("H") > 19){
                $this->jsonReturn($dataToken,'1030','闭盘时间');
            }
            $user = $this->userInfo;
            if(empty($user)){
                $this->jsonReturn($dataToken,'1020','未登录');
            }
            $user = ExUsers::itemById($user['id']);
            if($user['trading_password'] != md5(md5($password))){
                $this->jsonReturn($dataToken,'1011','交易密码错误');
            }
            $myType = ExExchangeRecord::itemsByUserIdAndType($user['id'],$type);
            if(empty($myType) || $myType['number'] < $number){
                $this->jsonReturn($dataToken,'1010','数量不足');
            }
            $save = OrderLogic::saveTransaction($number,$price,$type,2,$user['id']);
            $this->jsonReturn($dataToken);
        }
    }
    
    
    public function getGuadanDataAction(){
        if($this->request->isAjax()){
            $type = $this->request->getQuery('type','int',0);
            $where['status'] = 0;
            $where['currency_id'] = $type;
            $userInfo = $this->userInfo;
            if(empty($userInfo)){
                $this->jsonReturn([]);
            }
            $where['user_id'] = $userInfo['id'];
            $order = 'create_at desc';
            $items = ExGuadan::select($where,null,$order);
            if($items){
                $items = $items->toArray();
                foreach ($items as &$item){
                    $item['time'] = date("Y-m-d H:i",strtotime($item['create_at']));
                }
            }else{
                $items = [];
            }
            $this->jsonReturn($items);
        }
    }
    
    public function getOrderAction(){
        if($this->request->isAjax()){
            $type = $this->request->getQuery('type','int',0);
            $where['currency_id'] = $type;
            $where['pay_at'] = ['BETWEEN',[date("Y-m-d",time()),date("Y-m-d",time()+24*3600)]];
            $order = 'pay_at desc';
            $items = ExOrder::select($where,null,$order);
            if($items){
                $items = $items->toArray();
                foreach ($items as &$item){
                    $item['time'] = date("y/m/d H:i",strtotime($item['pay_at']));
                }
            }else{
                $items = [];
            }
            $this->jsonReturn($items);
        }
    }
    
    public function getAllGuandanAction(){
        if($this->request->isAjax()){
            $type = $this->request->getQuery('type','int',0);
            $where['status'] = 0;
            $where['currency_id'] = $type;
            $where['type'] = 1;
            $order = 'price desc,create_at desc';
            $itemsBuy = ExGuadan::select($where,null,$order,4);
            
            $where['type'] = 2;
            $itemsSale = ExGuadan::select($where,null,$order,4);
            
            if($itemsBuy){
                $itemsBuy = $itemsBuy->toArray();
                foreach ($itemsBuy as &$buy){
                    $buy['time'] = date("y/m/d H:i",strtotime($buy['create_at']));
                }
            }else{
                $itemsBuy = [];
            }
    
            if($itemsSale){
                $itemsSale = $itemsSale->toArray();
                foreach ($itemsSale as &$sale){
                    $sale['time'] = date("y/m/d H:i",strtotime($sale['create_at']));
                }
            }else{
                $itemsSale = [];
            }
            $data['buy'] = $itemsBuy;
            $data['sale'] = $itemsSale;
            $this->jsonReturn($data);
        }
    }
    
    
    public function getKDataAction(){
        $type = $this->request->getQuery('type','int',0);
        $obj = new ExOrder();
        $sql = 'SELECT
	i.close_price,
	i.open_price,
	i.date,
	IFNULL(oi.max,if(i.close_price > i.open_price,i.close_price,i.open_price)) as max,
	IFNULL(oi.min,if(i.close_price > i.open_price,i.open_price,i.close_price)) as min
FROM
	ex_initialization AS i
LEFT JOIN (
	SELECT
		DATE_FORMAT(o.pay_at, "%Y-%m-%d") AS date,
		min(o.price) AS min,
		max(o.price) AS max,
		SUM(o.number) AS deal_number
	FROM
		ex_order AS o
	WHERE
		o.currency_id = '.$type.'
	GROUP BY
		DATE_FORMAT(o.pay_at, "%Y-%m-%d")
) as oi ON oi.date = i.date
where i.currency_id = '.$type.';';
        $items = new Resultset(
            null,
            $obj,
            $obj->getReadConnection()->query($sql, null)
        );
        if(!empty($items)){
            $items = $items->toArray();
        }else{
            $items = [];
        }
        $this->jsonReturn($items);
    }
    
    /**
     * 撤单
     */
    public function cancelOrderAction(){
        $type = $this->request->getQuery('type','int',0);
        $id = $this->request->getQuery('id','int',0);
    }
}
