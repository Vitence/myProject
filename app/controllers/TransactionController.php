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
            
            //早8点晚7点
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
            
            if($type == 1){
                $this->jsonReturn($dataToken,'1050','暂时不对外开放');
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
            //早8点晚7点
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
            if($type == 1){
                $this->jsonReturn($dataToken,'1050','暂时不对外开放');
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
            $where['type'] = 1;
//            $where['pay_at'] = ['BETWEEN',[date("Y-m-d",time()),date("Y-m-d",time()+24*3600)]];
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
            $order = 'price asc,create_at asc';
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
                $itemsSale = array_reverse($itemsSale);
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
    IFNULL(oi.min,if(i.close_price > i.open_price,i.open_price,i.close_price)) as min,
    IFNULL(oi.deal_number,0) as deal_number
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
        $type = $this->request->getPost('type','int',0);
        $id = $this->request->getPost('id','int',0);
        $where['currency_id'] = $type;
        $where['id'] = $id;
        $where['status'] = 0;
        $item = ExGuadan::findRow($where);
        $userInfo = $this->userInfo;
        if(empty($userInfo)){
            self::redirect('/user/login');
        }
        if($item){
            $item = $item->toArray();
        }
        if($item){
            //我的买入订单
            //将我的锁定余额返还
            //取消的我挂单
            
            //我的卖出订单
            //将我的锁定数量返还
            //取消我的挂单
            
            $number = $item['surplus_number'];
            $price  = $item['price'];
            unset($where);
            if($item['type'] == 1){
                $where['id'] = $userInfo['id'];
                $user = ExUsers::findRow($where);
                $user = $user->toArray();
                $data['trad_balance'] = $user['trad_balance'] - $number * $price;
                $data['balance'] = $user['balance'] + $number * $price;
                ExUsers::updataData($where,$data);
                unset($where);
                unset($data);
                $where['id'] = $id;
                $data['status'] = 2;
                ExGuadan::updataData($where,$data);
            }else{
                $where['user_id'] = $userInfo['id'];
                $where['currency_id'] = $type;
                $recore = ExExchangeRecord::findRow($where);
                $recore = $recore->toArray();
                unset($where);
                unset($data);
                $where['id'] = $recore['id'];
                $data['frozen_number'] = $recore['frozen_number'] - $number;
                $data['number'] = $recore['number'] + $number;
                ExExchangeRecord::updataData($where,$data);
                unset($where);
                unset($data);
                $where['id'] = $id;
                $data['status'] = 2;
                ExGuadan::updataData($where,$data);
            }
            $this->jsonReturn('');
        }else{
            $this->jsonReturn('',1030,'交易不存在');
        }
    }
    
    public function getKOrderAction(){
        //获取最新价格
        $type = $this->request->getQuery('type','int',0);
        $newPrice = ExOrder::getMaxOrderPrice($type);
        if($newPrice){
            $newPrice = $newPrice->toArray();
        }else{
            $newPrice = [];
        }
        //最高价 最低价 总交易额 总数量
        $date = date("Y-m-d",time());
        $obj = new ExOrder();
        $sql = 'SELECT
    IFNULL(MAX(o.price),0) as max,
    IFNULL(MIN(o.price),0) as min,
    IFNULL(SUM(total_price),0) as total_price,
    IFNULL(SUM(number),0) as total_number,
    i.open_price
FROM
    ex_order AS o
LEFT JOIN ex_initialization AS i ON o.currency_id = i.currency_id AND DATE_FORMAT(o.pay_at, "%Y-%m-%d") = i.date
WHERE
    DATE_FORMAT(o.pay_at, "%Y-%m-%d") = "'.$date.'"
AND o.type = 1
AND o.currency_id = '.$type.'
GROUP BY
    o.currency_id;';
        $items = new Resultset(
            null,
            $obj,
            $obj->getReadConnection()->query($sql, null)
        );
        if(!empty($items)){
            $items = $items->toArray();
            $items = isset($items[0]) ? $items[0] : [];
        }else{
            $items = [];
        }
        if(empty($items)){
            $items['max'] = 0;
            $items['min'] = 0;
            $items['total_number'] = 0;
            $items['total_price'] = 0;
            $items['open_price'] = 0;
        }
        //最新价格
        $items['new_price'] = isset($newPrice['price']) ? $newPrice['price'] : 0;
        //涨幅度
        $items['rise'] = sprintf("%.2f", $items['new_price'] <= 0 || $items['open_price'] <=  0 ? 0 : ($items['new_price'] - $items['open_price']) / $items['open_price'] * 100);;
        //买一 卖一
        unset($where);
        $where['status'] = 0;
        $where['currency_id'] = $type;
        $where['type'] = 1;
        $order = 'price desc,create_at desc';
        $itemsBuy = ExGuadan::findRow($where,null,$order);
    
        $where['type'] = 2;
        $order = 'price asc,create_at asc';
        $itemsSale = ExGuadan::findRow($where,null,$order);
    
        if($itemsBuy){
            $itemsBuy = $itemsBuy->toArray();
        }
    
        if($itemsSale){
            $itemsSale = $itemsSale->toArray();
        }
        
        $items['buy_first'] = !empty($itemsBuy) ? $itemsBuy['price']  : 0;
        $items['sale_first'] = !empty($itemsSale) ? $itemsSale['price']  : 0;
        
        $this->jsonReturn($items);
    }
    
    
    public function kAction(){
        //获取历史价格
        $type = $this->request->getQuery('symbol','int',1);
        $where['currency_id'] = $type;
        $where['date'] = ['<',\Util\common::getDate()];
        $historys = ExKHistory::select($where);
        if($historys){
            $historys = $historys->toArray();
            $datas = [];
            foreach ($historys as $key => &$history){
                    $datas[$key][] = strtotime($history['date']) * 1000;
                    $datas[$key][] = (float)$history['open_price'];
                    $datas[$key][] = (float)$history['max_price'];
                    $datas[$key][] = (float)$history['min_price'];
                    $datas[$key][] = (float)$history['close_price'];
                    $datas[$key][] = (float)$history['total_number'];
            }
        }
         unset($where);
         unset($whereInit);
         //最后一次的收盘价
        $initslast = ExInitialization::findRow(array('currency_id'=>$type),null,'date desc');
        $initslast = $initslast->toArray();
    
        //如果最后一次的日期是今天的
        //当日的开盘价
         $newPrice = ExOrder::getTodayMaxOrderPrice($type);//最新价格
         if($newPrice){
             $newPrice = $newPrice->toArray();
         }else{
             $newPrice = [];
         }
         //最高价 最低价 总交易额 总数量
         $date = date("Y-m-d",time());
         $obj = new ExOrder();
         $sql = 'SELECT
     IFNULL(MAX(o.price),0) as max,
     IFNULL(MIN(o.price),0) as min,
     IFNULL(SUM(number),0) as total_number,
     i.open_price
 FROM
     ex_order AS o
 LEFT JOIN ex_initialization AS i ON o.currency_id = i.currency_id AND DATE_FORMAT(o.pay_at, "%Y-%m-%d") = i.date
 WHERE
     DATE_FORMAT(o.pay_at, "%Y-%m-%d") = "'.$date.'"
 AND o.type = 1
 AND o.currency_id = '.$type.'
 GROUP BY
     o.currency_id;';
         $items = new Resultset(
             null,
             $obj,
             $obj->getReadConnection()->query($sql, null)
         );
         if(!empty($items)){
             $items = $items->toArray();
             $items = isset($items[0]) ? $items[0] : [];
         }else{
             $items = [];
         }
         if(empty($items)){
             $items['max'] = $initslast['date'] == date("Y-m-d",time()) ? $initslast['open_price'] : $initslast['close_price'] ;
             $items['min'] = $initslast['date'] == date("Y-m-d",time()) ? $initslast['open_price'] : $initslast['close_price'] ;
             $items['total_number'] = 0;
             $items['total_price'] = 0;
             $items['open_price'] = $initslast['date'] == date("Y-m-d",time()) ? $initslast['open_price'] : $initslast['close_price'] ;
         }
         //最新价格
        $items['new_price'] = isset($newPrice['price']) ? (float)$newPrice['price'] : ($initslast['date'] == date("Y-m-d",time()) ? $initslast['open_price'] : $initslast['close_price']);
        $newData[] = strtotime(\Util\common::getDate()) * 1000;
         $newData[] = (float)$items['open_price'];
         $newData[] = (float)$items['max'];
         $newData[] = (float)$items['min'];
         $newData[] = (float)$items['new_price'];
         $newData[] = (float)$items['total_number'];
         $datas[] = $newData;
        $data['lines'] = $datas;
        $this->jsonReturnTest($data);
    }
}
