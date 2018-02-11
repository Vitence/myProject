<?php
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
class OrderLogic{
    
    public static function txManager() {
        $txManager = new TxManager();
        return $txManager;
    }
    
    public static function saveTransaction(&$number,$price,$type,$handle=1,$userId){
        //买入
        $add = self::addGuadan($type,$price,$userId,$handle,$number);
        $whereGuadan['id'] = $add;
        //按价格和挂单时间从旧到新的排序 找对应的的
        $items  = self::getTypeList($type,$price,$userId,$handle);
        $data['currency_id'] = $type;
        $data['price'] = $price;
        $data['user_id'] = $userId;
        $data['create_at'] = \Util\common::getDataTime();
        $data['update_at'] = \Util\common::getDataTime();
        $data['pay_at'] = \Util\common::getDataTime();
        $data['type'] = $handle;
        
        $user = ExUsers::itemById($userId);
        
        if($handle == 1){
            //减去我的余额（将要买的余额设置为锁定余额）
            $dataUser['balance'] = $user['balance'] - ($number * $price);
            $dataUser['trad_balance'] = $user['trad_balance'] + $number * $price;
            ExUsers::saveDataById($userId,$dataUser);
            $_SESSION['userInfo']['balance'] = $user['balance'] - ($number * $price);
            if(!empty($items)){
                $i = 0;
                while ($number > 0){
                    if(isset($items[$i])){
                        //别人卖的大于或等于我买的
                        if($items[$i]['surplus_number'] >= $number){
                            $guandanData['status'] = 1;  //我的挂单完成
                            $guandanData['surplus_number'] = 0; //我的本次购买数为0
                            //别人的挂单
                            $updateData['surplus_number'] = $items[$i]['surplus_number'] - $number; //别人的挂单继续挂单，挂单数量减去我买入的
                            $updateData['status'] = $items[$i]['surplus_number'] == $number ? 1 : 0; //如果卖的等于我卖的。则别人的挂单也完成，否则继续挂单状态
                            //订单数量
                            $myOrderNumber = $number;
//                            $otherOrderNumber = $items[$i]['surplus_number'] - $number;
                            $otherOrderNumber = $number;
                        }
                        //别人卖的小于我要买的
                        else{
                            $guandanData['status'] = 0; //我的继续挂单
                            $guandanData['surplus_number'] = $number - $items[$i]['surplus_number']; //我的挂单购买数量 减去此次我买如的一部分
                            
                            $updateData['surplus_number'] = 0; //别人挂单数量为0
                            $updateData['status'] = 1; //别人的挂单完成
    
//                            $myOrderNumber = $number - $items[$i]['surplus_number'];
                            $myOrderNumber = $items[$i]['surplus_number'];
                            $otherOrderNumber = $items[$i]['surplus_number'];
                        }
                        //更新我的挂单状态
                        ExGuadan::updateById($add,$guandanData);
                        //更新他人的挂单状态
                        ExGuadan::updateById($items[$i]['id'],$updateData);
                        
                        $user = ExUsers::itemById($userId);
                        //减去我锁定的余额
                        $dataUser['trad_balance'] = $user['trad_balance'] - ($myOrderNumber * $price);
                        ExUsers::saveDataById($userId,$dataUser);
                        
                        //生成我的订单
                        unset($myOrderData);
                        $myOrderData = $data;
                        $myOrderData['pay_at'] = \Util\common::getDataTime();
                        $myOrderData['number'] = $myOrderNumber;
                        $myOrderData['total_price'] = $myOrderNumber * $price;
                        unset($obj);
                        $obj = new ExOrder();
                        ExOrder::addData($obj,$myOrderData);
                        //增加我的币种类型数量余额
                        $myType = ExExchangeRecord::itemsByUserIdAndType($userId,$type);
                        unset($dataType);
                        unset($typeWhere);
                        if(empty($myType)){
                            $dataType['user_id'] = $userId;
                            $dataType['currency_id'] = $type;
                            $dataType['number'] = $myOrderNumber;
                            $dataType['create_at'] = \Util\common::getDataTime();
                            $dataType['update_at'] = \Util\common::getDataTime();
                            $typeObj = new ExExchangeRecord();
                            ExExchangeRecord::addData($typeObj,$data);
                        }else{
                            $typeWhere['user_id'] = $userId;
                            $typeWhere['currency_id'] = $type;
                            $dataType['number'] = $myType['number'] + $myOrderNumber;
                            $dataType['update_at'] = \Util\common::getDataTime();
                            ExExchangeRecord::updataData($typeWhere,$dataType);
                        }
                        //别人的订单 手续费
                        $otherOrderData = $data;
                        $otherOrderData['user_id'] = $items[$i]['user_id'];
                        $otherOrderData['type'] = 2;
                        $procedures = $otherOrderNumber * $price * 0.1;
                        $otherOrderData['procedures'] = $procedures;
                        $otherOrderData['total_price'] = $otherOrderNumber * $price - $procedures;
                        $otherOrderData['number'] = $otherOrderNumber;
                        unset($obj);
                        $obj = new ExOrder();
                        ExOrder::addData($obj,$otherOrderData);
                        
                        //减少卖方币种锁定数量
                        unset($typeWhere);
                        unset($dataType);
                        $otherRecord = ExExchangeRecord::itemsByUserIdAndType($items[$i]['user_id'],$type);
                        $dataType['frozen_number'] = $otherRecord['frozen_number'] - $otherOrderNumber;
                        $typeWhere['user_id'] = $items[$i]['user_id'];
                        $typeWhere['currency_id'] = $type;
                        ExExchangeRecord::updataData($typeWhere,$dataType);
                        //增加别人的余额
                        unset($user);
                        unset($dataUser);
                        $user = ExUsers::itemById($items[$i]['user_id']);
                        $dataUser['trad_balance'] = $user['trad_balance'] + $otherOrderData['total_price'];
                        ExUsers::saveDataById($items[$i]['user_id'],$dataUser);
                        //我的数量为负数 结束循环
                        $number = $number - $items[$i]['number'];
                        $i++;
                    }else{
                        $number = 0;
                    }
                }
            }
        }else{
            //将我卖出的币种设置为锁定
            $myType = ExExchangeRecord::itemsByUserIdAndType($userId,$type);
            $whereType['user_id'] = $userId;
            $whereType['currency_id'] = $type;
            $dataType['number'] = $myType['number'] - $number;
            $dataType['frozen_number'] = $myType['frozen_number'] + $number;
            $dataType['update_at'] = \Util\common::getDataTime();
            ExExchangeRecord::updataData($whereType,$dataType);
            if(!empty($items)){
                $i = 0;
                while ($number > 0){
                    if(isset($items[$i])){
                        //别人买的大于或等于我要卖的
                        if($items[$i]['surplus_number'] >= $number){
                            //我的挂单
                            $guandanData['status'] = 1;   //我的挂单完成
                            $guandanData['surplus_number'] = 0;  //我的卖出数量为0
                            //别人的挂单
                            $updateData['surplus_number'] = $items[$i]['surplus_number'] - $number;  //别人的买的挂单数量为 总买的减去我此次卖的
                            $updateData['status'] = $items[$i]['surplus_number'] == $number ? 1 : 0; //如果买的等于别人卖的。则别人的挂单也完成，否则继续挂单状态
                            //订单数量
                            $myOrderNumber = $number;   //我的订单数量为卖出的数量
//                            $otherOrderNumber = $items[$i]['surplus_number'] - $number; //别人的订单数量为此次买如的数量
                            $otherOrderNumber =  $number; //别人的订单数量为此次买如的数量
                        } else{
                            //剩余的继续挂单
                            $guandanData['surplus_number'] = $number - $items[$i]['surplus_number'];
                            $guandanData['status'] = 0;
                            //别人的挂单
                            $updateData['surplus_number'] = 0;
                            $updateData['status'] = 1;
                            //订单数量
                            $myOrderNumber = $items[$i]['surplus_number'];   //我的订单数量为卖出的数量
//                            $otherOrderNumber = $number - $items[$i]['surplus_number']; //别人的订单数量为此次买如的数量
                            $otherOrderNumber =  $items[$i]['surplus_number']; //别人的订单数量为此次买如的数量
                        }
                        //更新我的挂单状态
                        ExGuadan::updateById($add,$guandanData);
                        //更新他人的挂单状态
                        ExGuadan::updateById($items[$i]['id'],$updateData);
                        
                        //减去我的币种锁定数量
                        unset($whereUser);
                        unset($dataUser);
                        $myType = ExExchangeRecord::itemsByUserIdAndType($userId,$type);
                        $whereUser['user_id'] = $userId;
                        $whereUser['currency_id'] = $type;
                        $dataUser['frozen_number'] = $myType['frozen_number'] - $myOrderNumber;
                        $dataUser['update_at'] = \Util\common::getDataTime();
                        ExExchangeRecord::updataData($whereUser,$dataUser);
                        //我的订单
                        $myOrderData = $data;
                        $myOrderData['number'] = $myOrderNumber;
                        $procedures = $myOrderNumber * $price * 0.1;
                        $myOrderData['procedures'] = $procedures;
                        $myOrderData['total_price'] = $myOrderNumber * $price - $procedures;
                        $obj = new ExOrder();
                        ExOrder::addData($obj,$myOrderData);
                        //增加我的余额
                        unset($whereUser);
                        unset($dataUser);
                        $user = ExUsers::itemById($items[$i]['user_id']);
                        $whereUser['id'] = $user['id'];
                        $dataUser['balance'] = $user['balance'] + $myOrderData['total_price'];
                        ExUsers::updataData($whereUser,$dataUser);
                        $_SESSION['userInfo']['balance'] = $dataUser['balance'];
                        //别人的订单
                        $otherOrderData = $data;
                        $otherOrderData['user_id'] = $items[$i]['user_id'];
                        $otherOrderData['type'] = 1;
                        $otherOrderData['total_price'] = $otherOrderNumber * $price;
                        $otherOrderData['number'] = $otherOrderNumber;
                        unset($obj);
                        $obj = new ExOrder();
                        ExOrder::addData($obj,$otherOrderData);
                        //减少别人的锁定余额
                        unset($whereUser);
                        unset($dataUser);
                        $whereUser['id'] = $items[$i]['user_id'];
                        $dataUser['trad_balance'] = $user['trad_balance'] - $otherOrderData['total_price'];
                        ExUsers::updataData($whereUser,$dataUser);
                        //增加别人的币种数量
                        $otherType = ExExchangeRecord::itemsByUserIdAndType($items[$i]['user_id'],$type);
                        unset($dataType);
                        unset($typeWhere);
                        unset($typeObj);
                        if(empty($otherType)){
                            $dataType['user_id'] = $items[$i]['user_id'];
                            $dataType['currency_id'] = $type;
                            $dataType['number'] = $otherOrderNumber;
                            $dataType['create_at'] = \Util\common::getDataTime();
                            $dataType['update_at'] = \Util\common::getDataTime();
                            $typeObj = new ExExchangeRecord();
                            ExExchangeRecord::addData($typeObj,$data);
                        }else{
                            $typeWhere['user_id'] = $items[$i]['user_id'];
                            $typeWhere['currency_id'] = $type;
                            $dataType['number'] = $otherType['number'] + $otherOrderNumber;
                            ExExchangeRecord::updataData($typeWhere,$dataType);
                        }
                        
                        //我的数量为负数 结束循环
                        $number = $number - $items[$i]['number'];
                        $i++;
                    }else{
                        $number = 0;
                    }
                }
            }
        }
        return true;
    }
    
    public static function getTypeList($type,$price,$userId,$handle){
        $where['type'] = $handle == 1 ? 2 : 1;
        $where['status'] = 0;
        $where['currency_id'] = $type;
        $where['price'] = $price;
        $where['user_id'] = ['!=',$userId];
        $order = 'create_at asc';
        $items  = ExGuadan::select($where,null,$order);
        return $items ? $items->toArray() : [];
    }
    
    
    public static function addGuadan($type,$price,$userId,$handle,$number){
        $data['currency_id'] = $type;
        $data['price'] = $price;
        $data['user_id'] = $userId;
        $data['create_at'] = \Util\common::getDataTime();
        $data['update_at'] = \Util\common::getDataTime();
        $data['type'] = $handle;
        $data['status'] = 0;
        $data['number'] = $number;
        $data['surplus_number'] = $number;
        $obj = new ExGuadan();
        ExGuadan::addData($obj,$data);
        return $obj->id;
    }
}