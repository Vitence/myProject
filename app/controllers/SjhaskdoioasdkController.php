<?php
ignore_user_abort();//关闭浏览器仍然执行
set_time_limit(0);//让程序一直执行下去   4679
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
class SjhaskdoioasdkController extends ControllerBase{
    
    /**
     * 整天执行
     */
    public function shellAction(){
        try{
            $dateTime   = \Util\common::getDate(); //当天日期
//            $currencyId = 1; //币种 1  //此币种只针对两个测试账户，其他用户只能看不能操作。
            $whereShell['date'] = $dateTime;
//            $whereShell['currency_id'] = $currencyId;
            $shellInfo = ExShell::select($whereShell);
            if($shellInfo){
                $shellInfo = $shellInfo->toArray();
                //脚本结束时间
                $endTime   = strtotime(\Util\common::getDate()." 16:59:57");
                //保存开盘和关盘价格
                foreach ($shellInfo as $item){
                    $openPrice  = $item['open_price'];  //开价
                    $closePrice = $item['close_price'];  //关价
                    $this->saveInit($dateTime,$openPrice,$closePrice,$item['currency_id']);
                }
                //每天五点结束
                while(time() <= $endTime){
                    //执行间隔
                    $sleep   = rand(2,5);
                    foreach ($shellInfo as $item){
                        $currencyId = $item['currency_id'];
                        $openPrice = $item['open_price'];
                        $maxPrice =  $item['max_price'];
                        $minPrice =  $item['min_price'];
                        //制定涨、跌概率
                        $updownNumberBai = rand(0,100);
                        //浸日最新成交价
                        $newPrice = ExOrder::getTodayMaxOrderPrice($currencyId);
                        $newPriceNumber = 0;
                        if($newPrice){
                            $newPrice = $newPrice->toArray();
                            if(!empty($newPrice)){
                                $newPriceNumber = $newPrice['price'];
                            }
                        }
                        $prevPrice = $newPriceNumber > 0 ? $newPriceNumber : $openPrice;
                        
                        //上一次成交价*0---0.2的随机数 即上涨或跌的价格
                        $updownNumber = $prevPrice * \Util\common::randomFloat();
                        
                        $nowPrice = 0; //本次生成数据价格
                        if($updownNumberBai > 25){ //涨
                            $nowPrice = (($prevPrice + $updownNumber) <= $maxPrice) ? ($prevPrice + $updownNumber) : $maxPrice;
                        }else{  //跌
                            $nowPrice = (($prevPrice - $updownNumber) >= $minPrice) ? ($prevPrice - $updownNumber) : $minPrice;
                        }
                        if($nowPrice > 0){
                            $orderNumber = rand(10,5000);
                            $admin1  = ExUsers::itemById(9); //账号1
                            $admin2  = ExUsers::itemById(10); //账号2
                            //两个账号币种的数量
                            $admin1Number = ExExchangeRecord::itemsByUserIdAndType(9,$currencyId);
                            $admin2Number = ExExchangeRecord::itemsByUserIdAndType(10,$currencyId);
                            //先生成一条成交记录 一买单 一卖单 价格数量总价格
                            $orderData['currency_id'] = $currencyId;
                            $orderData['number']      = $orderNumber;
                            $orderData['price']       = $nowPrice;
                            $orderData['total_price'] = $orderNumber * $nowPrice;
                            $orderData['create_at']  = \Util\common::getDataTime();
                            $orderData['update_at']  = \Util\common::getDataTime();
                            $orderData['pay_at']     = \Util\common::getDataTime();
                            $orderData['user_id']    = '';
                            $orderData['type']       = '';
                            $orderData['procedures'] = '';
                            //根据谁的数量多谁的为卖单
                            if($admin1Number['number'] >= $admin2Number['number']){ //admin1为卖单
                                //生成admin1的交易记录 卖出
                                $admin1Data = $orderData;
                                $admin1Data['user_id']    = $admin1['id'];
                                $admin1Data['type']       = 2;
                                $admin1Data['procedures'] = $admin1Data['total_price'] * 0.1;
                                $admin1Data['total_price'] = $admin1Data['total_price'] - $admin1Data['procedures'];
                                //生成admin2的交易记录 买入
                                $admin2Data = $orderData;
                                $admin2Data['user_id']    = $admin2['id'];
                                $admin2Data['type']       = 1;
                                unset($obj);
                                $obj = new ExOrder();
                                ExOrder::addData($obj,$admin1Data);
                                unset($obj);
                                $obj = new ExOrder();
                                ExOrder::addData($obj,$admin2Data);
                                //减去admin1的数量
                                $this->reduceNumber($admin1['id'],$currencyId,$orderNumber);
                                //增加admin1的余额 （减手续费）
                                $this->plusPrice($admin1['id'],$orderNumber * $nowPrice);
                                //增加admin2的数量
                                $this->plusNumber($admin2['id'],$currencyId,$orderNumber);
                                //减去admin2的余额
                                $this->reducePrice($admin2['id'],$admin2Data['total_price']);
                            }else{ //admin2为卖单
                                //生成admin2的交易记录 卖出
                                $admin2Data = $orderData;
                                $admin2Data['user_id']    = $admin2['id'];
                                $admin2Data['type']       = 2;
                                $admin2Data['procedures'] = $admin2Data['total_price'] * 0.1;
                                $admin2Data['total_price'] = $admin2Data['total_price'] - $admin2Data['procedures'];
                                //生成admin1的交易记录 买入
                                $admin1Data = $orderData;
                                $admin1Data['user_id']    = $admin1['id'];
                                $admin1Data['type']       = 1;
                                unset($obj);
                                $obj = new ExOrder();
                                ExOrder::addData($obj,$admin1Data);
                                unset($obj);
                                $obj = new ExOrder();
                                ExOrder::addData($obj,$admin2Data);
                                //减去admin2的数量
                                $this->reduceNumber($admin2['id'],$currencyId,$orderNumber);
                                //增加admin2的余额 （减手续费）
                                $this->plusPrice($admin2['id'],$orderNumber * $nowPrice);
                                //增加admin1的数量
                                $this->plusNumber($admin1['id'],$currencyId,$orderNumber);
                                //减去admin1的余额
                                $this->reducePrice($admin1['id'],$admin1Data['total_price']);
                            }
                            //将两个账号的所有此币种的挂单记录全部撤单。
                            $whereGuadan['currency_id'] = $currencyId;
                            $whereGuadan['user_id'] = $admin1['id'];
                            $whereGuadan['status'] = 0;
                            $dataaGuandan['status'] = 2; //撤单状态
                            $dataaGuandan['update_at'] = \Util\common::getDataTime();
                            ExGuadan::updataAllData($whereGuadan,$dataaGuandan);
                            //生成挂单记录 每个账号8个单 四个买单和卖单 不匹配 只挂单。
                            $initBuyPrice = $nowPrice;
                            $initSalePrice = $nowPrice;
                            unset($guadanData);
                            $guadanData['user_id'] = $admin1['id'];
                            $guadanData['currency_id'] = $currencyId;
                            $guadanData['create_at'] = \Util\common::getDataTime();
                            $guadanData['update_at'] = \Util\common::getDataTime();
                            for ($i = 0; $i < 8 ; $i++){
                                //小于4的时候是四个买单
                                //大于等于4的时候是四个卖单
                                if($i < 4){
                                    $priceEd = \Util\common::randomFloat(0.01,0.05);
                                    $initBuyPrice = $initBuyPrice - $priceEd;
                                    $guadanData['price'] = $initBuyPrice;
                                    $numberEd = rand(10,5000);
                                    $guadanData['number'] = $numberEd;
                                    $guadanData['surplus_number'] = $numberEd;
                                    $guadanData['type'] = 1;
                                    unset($obj);
                                    $obj = new ExGuadan();
                                    ExGuadan::addData($obj,$guadanData);
                                }else{
                                    $priceEd = \Util\common::randomFloat(0.01,1);
                                    $initSalePrice = $initSalePrice + $priceEd;
                                    $guadanData['price'] = $initSalePrice;
                                    $numberEd = rand(10,1000);
                                    $guadanData['number'] = $numberEd;
                                    $guadanData['surplus_number'] = $numberEd;
                                    $guadanData['type'] = 2;
                                    unset($obj);
                                    $obj = new ExGuadan();
                                    ExGuadan::addData($obj,$guadanData);
                                }
                            }
                        }
                    }
                    sleep($sleep);
                }
            }
        }catch (\Exception $e){
            echo $e->getCode().'<br>';
            echo $e->getMessage();
        }
    }
    
    
    
    /**
     * 每天晚上17点05秒执行
     * sleep
     */
    public function checkAction(){
        sleep(5);
        //检查当日价格是否有最高价和最低价出现，如果没有，则将最接近最高价的数据记录改成最高价格，最接近最低价的数据记录改成最低价，并生成一条17:00的收盘成交价数据，价格为收盘价，数量按照上面的规则即可
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 1;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            $openPrice = $shellInfo['open_price'];  //开价
            $closePrice = $shellInfo['close_price'];  //关价
            $maxPrice = $shellInfo['max_price'];  //最高
            $minPrice = $shellInfo['min_price'];  //最低
            //查询最高价
            $whereMax['currency_id'] = $currencyId;
            $whereMax['user_id'] = 9;
            $whereMax['pay_at'] = ['between',[$dateTime." 00:00:01",$dateTime." 23:59:59"]];
            $order = 'price desc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最高价的话讲最接近对最高价的改为最该嫁
            if($item['price'] != $maxPrice){
                $data['price'] = $maxPrice;
                $data['total_price'] = $item['number'] * $maxPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //检查最低价
            $order = 'price asc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最低价的话将接近最低价的改为最低价
            if($item['price'] != $minPrice){
                $data['price'] = $minPrice;
                $data['total_price'] = $item['number'] * $minPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //生成一条交易记录 价格为收盘价 数量根据 10～5000随机整数
            $orderNumber = rand(10,5000);
            $admin1  = ExUsers::itemById(9); //账号1
            $admin2  = ExUsers::itemById(10); //账号2
            //两个账号币种的数量
            $admin1Number = ExExchangeRecord::itemsByUserIdAndType(9,$currencyId);
            $admin2Number = ExExchangeRecord::itemsByUserIdAndType(10,$currencyId);
            //先生成一条成交记录 一买单 一卖单 价格数量总价格
            $nowPrice = $closePrice;
            $orderData['currency_id'] = $currencyId;
            $orderData['number']      = $orderNumber;
            $orderData['price']       = $nowPrice;
            $orderData['total_price'] = $orderNumber * $nowPrice;
            $orderData['create_at']  = \Util\common::getDataTime();
            $orderData['update_at']  = \Util\common::getDataTime();
            $orderData['pay_at']     = \Util\common::getDataTime();
            //根据谁的数量多谁的为卖单
            if($admin1Number['number'] >= $admin2Number['number']){ //admin1为卖单
                //生成admin1的交易记录 卖出
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 2;
                $admin1Data['procedures'] = $admin1Data['total_price'] * 0.1;
                $admin1Data['total_price'] = $admin1Data['total_price'] - $admin1Data['procedures'];
                //生成admin2的交易记录 买入
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin1的数量
                $this->reduceNumber($admin1['id'],$currencyId,$orderNumber);
                //增加admin1的余额 （减手续费）
                $this->plusPrice($admin1['id'],$orderNumber * $nowPrice);
                //增加admin2的数量
                $this->plusNumber($admin2['id'],$currencyId,$orderNumber);
                //减去admin2的余额
                $this->reducePrice($admin2['id'],$admin2Data['total_price']);
            }else{ //admin2为卖单
                //生成admin2的交易记录 卖出
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 2;
                $admin2Data['procedures'] = $admin2Data['total_price'] * 0.1;
                $admin2Data['total_price'] = $admin2Data['total_price'] - $admin2Data['procedures'];
                //生成admin1的交易记录 买入
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin2的数量
                $this->reduceNumber($admin2['id'],$currencyId,$orderNumber);
                //增加admin2的余额 （减手续费）
                $this->plusPrice($admin2['id'],$orderNumber * $nowPrice);
                //增加admin1的数量
                $this->plusNumber($admin1['id'],$currencyId,$orderNumber);
                //减去admin1的余额
                $this->reducePrice($admin1['id'],$admin1Data['total_price']);
            }
        }
    }
    public function check4Action(){
        sleep(8);
        //检查当日价格是否有最高价和最低价出现，如果没有，则将最接近最高价的数据记录改成最高价格，最接近最低价的数据记录改成最低价，并生成一条17:00的收盘成交价数据，价格为收盘价，数量按照上面的规则即可
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 4;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            $openPrice = $shellInfo['open_price'];  //开价
            $closePrice = $shellInfo['close_price'];  //关价
            $maxPrice = $shellInfo['max_price'];  //最高
            $minPrice = $shellInfo['min_price'];  //最低
            //查询最高价
            $whereMax['currency_id'] = $currencyId;
            $whereMax['user_id'] = 9;
            $whereMax['pay_at'] = ['between',[$dateTime." 00:00:01",$dateTime." 23:59:59"]];
            $order = 'price desc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最高价的话讲最接近对最高价的改为最该嫁
            if($item['price'] != $maxPrice){
                $data['price'] = $maxPrice;
                $data['total_price'] = $item['number'] * $maxPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //检查最低价
            $order = 'price asc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最低价的话将接近最低价的改为最低价
            if($item['price'] != $minPrice){
                $data['price'] = $minPrice;
                $data['total_price'] = $item['number'] * $minPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //生成一条交易记录 价格为收盘价 数量根据 10～5000随机整数
            $orderNumber = rand(10,5000);
            $admin1  = ExUsers::itemById(9); //账号1
            $admin2  = ExUsers::itemById(10); //账号2
            //两个账号币种的数量
            $admin1Number = ExExchangeRecord::itemsByUserIdAndType(9,$currencyId);
            $admin2Number = ExExchangeRecord::itemsByUserIdAndType(10,$currencyId);
            //先生成一条成交记录 一买单 一卖单 价格数量总价格
            $nowPrice = $closePrice;
            $orderData['currency_id'] = $currencyId;
            $orderData['number']      = $orderNumber;
            $orderData['price']       = $nowPrice;
            $orderData['total_price'] = $orderNumber * $nowPrice;
            $orderData['create_at']  = \Util\common::getDataTime();
            $orderData['update_at']  = \Util\common::getDataTime();
            $orderData['pay_at']     = \Util\common::getDataTime();
            //根据谁的数量多谁的为卖单
            if($admin1Number['number'] >= $admin2Number['number']){ //admin1为卖单
                //生成admin1的交易记录 卖出
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 2;
                $admin1Data['procedures'] = $admin1Data['total_price'] * 0.1;
                $admin1Data['total_price'] = $admin1Data['total_price'] - $admin1Data['procedures'];
                //生成admin2的交易记录 买入
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin1的数量
                $this->reduceNumber($admin1['id'],$currencyId,$orderNumber);
                //增加admin1的余额 （减手续费）
                $this->plusPrice($admin1['id'],$orderNumber * $nowPrice);
                //增加admin2的数量
                $this->plusNumber($admin2['id'],$currencyId,$orderNumber);
                //减去admin2的余额
                $this->reducePrice($admin2['id'],$admin2Data['total_price']);
            }else{ //admin2为卖单
                //生成admin2的交易记录 卖出
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 2;
                $admin2Data['procedures'] = $admin2Data['total_price'] * 0.1;
                $admin2Data['total_price'] = $admin2Data['total_price'] - $admin2Data['procedures'];
                //生成admin1的交易记录 买入
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin2的数量
                $this->reduceNumber($admin2['id'],$currencyId,$orderNumber);
                //增加admin2的余额 （减手续费）
                $this->plusPrice($admin2['id'],$orderNumber * $nowPrice);
                //增加admin1的数量
                $this->plusNumber($admin1['id'],$currencyId,$orderNumber);
                //减去admin1的余额
                $this->reducePrice($admin1['id'],$admin1Data['total_price']);
            }
        }
    }
    public function check6Action(){
        sleep(10);
        //检查当日价格是否有最高价和最低价出现，如果没有，则将最接近最高价的数据记录改成最高价格，最接近最低价的数据记录改成最低价，并生成一条17:00的收盘成交价数据，价格为收盘价，数量按照上面的规则即可
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 6;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            $openPrice = $shellInfo['open_price'];  //开价
            $closePrice = $shellInfo['close_price'];  //关价
            $maxPrice = $shellInfo['max_price'];  //最高
            $minPrice = $shellInfo['min_price'];  //最低
            //查询最高价
            $whereMax['currency_id'] = $currencyId;
            $whereMax['user_id'] = 9;
            $whereMax['pay_at'] = ['between',[$dateTime." 00:00:01",$dateTime." 23:59:59"]];
            $order = 'price desc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最高价的话讲最接近对最高价的改为最该嫁
            if($item['price'] != $maxPrice){
                $data['price'] = $maxPrice;
                $data['total_price'] = $item['number'] * $maxPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //检查最低价
            $order = 'price asc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最低价的话将接近最低价的改为最低价
            if($item['price'] != $minPrice){
                $data['price'] = $minPrice;
                $data['total_price'] = $item['number'] * $minPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //生成一条交易记录 价格为收盘价 数量根据 10～5000随机整数
            $orderNumber = rand(10,5000);
            $admin1  = ExUsers::itemById(9); //账号1
            $admin2  = ExUsers::itemById(10); //账号2
            //两个账号币种的数量
            $admin1Number = ExExchangeRecord::itemsByUserIdAndType(9,$currencyId);
            $admin2Number = ExExchangeRecord::itemsByUserIdAndType(10,$currencyId);
            //先生成一条成交记录 一买单 一卖单 价格数量总价格
            $nowPrice = $closePrice;
            $orderData['currency_id'] = $currencyId;
            $orderData['number']      = $orderNumber;
            $orderData['price']       = $nowPrice;
            $orderData['total_price'] = $orderNumber * $nowPrice;
            $orderData['create_at']  = \Util\common::getDataTime();
            $orderData['update_at']  = \Util\common::getDataTime();
            $orderData['pay_at']     = \Util\common::getDataTime();
            //根据谁的数量多谁的为卖单
            if($admin1Number['number'] >= $admin2Number['number']){ //admin1为卖单
                //生成admin1的交易记录 卖出
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 2;
                $admin1Data['procedures'] = $admin1Data['total_price'] * 0.1;
                $admin1Data['total_price'] = $admin1Data['total_price'] - $admin1Data['procedures'];
                //生成admin2的交易记录 买入
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin1的数量
                $this->reduceNumber($admin1['id'],$currencyId,$orderNumber);
                //增加admin1的余额 （减手续费）
                $this->plusPrice($admin1['id'],$orderNumber * $nowPrice);
                //增加admin2的数量
                $this->plusNumber($admin2['id'],$currencyId,$orderNumber);
                //减去admin2的余额
                $this->reducePrice($admin2['id'],$admin2Data['total_price']);
            }else{ //admin2为卖单
                //生成admin2的交易记录 卖出
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 2;
                $admin2Data['procedures'] = $admin2Data['total_price'] * 0.1;
                $admin2Data['total_price'] = $admin2Data['total_price'] - $admin2Data['procedures'];
                //生成admin1的交易记录 买入
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin2的数量
                $this->reduceNumber($admin2['id'],$currencyId,$orderNumber);
                //增加admin2的余额 （减手续费）
                $this->plusPrice($admin2['id'],$orderNumber * $nowPrice);
                //增加admin1的数量
                $this->plusNumber($admin1['id'],$currencyId,$orderNumber);
                //减去admin1的余额
                $this->reducePrice($admin1['id'],$admin1Data['total_price']);
            }
        }
    }
    public function check7Action(){
        sleep(12);
        //检查当日价格是否有最高价和最低价出现，如果没有，则将最接近最高价的数据记录改成最高价格，最接近最低价的数据记录改成最低价，并生成一条17:00的收盘成交价数据，价格为收盘价，数量按照上面的规则即可
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 7;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            $openPrice = $shellInfo['open_price'];  //开价
            $closePrice = $shellInfo['close_price'];  //关价
            $maxPrice = $shellInfo['max_price'];  //最高
            $minPrice = $shellInfo['min_price'];  //最低
            //查询最高价
            $whereMax['currency_id'] = $currencyId;
            $whereMax['user_id'] = 9;
            $whereMax['pay_at'] = ['between',[$dateTime." 00:00:01",$dateTime." 23:59:59"]];
            $order = 'price desc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最高价的话讲最接近对最高价的改为最该嫁
            if($item['price'] != $maxPrice){
                $data['price'] = $maxPrice;
                $data['total_price'] = $item['number'] * $maxPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //检查最低价
            $order = 'price asc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最低价的话将接近最低价的改为最低价
            if($item['price'] != $minPrice){
                $data['price'] = $minPrice;
                $data['total_price'] = $item['number'] * $minPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //生成一条交易记录 价格为收盘价 数量根据 10～5000随机整数
            $orderNumber = rand(10,5000);
            $admin1  = ExUsers::itemById(9); //账号1
            $admin2  = ExUsers::itemById(10); //账号2
            //两个账号币种的数量
            $admin1Number = ExExchangeRecord::itemsByUserIdAndType(9,$currencyId);
            $admin2Number = ExExchangeRecord::itemsByUserIdAndType(10,$currencyId);
            //先生成一条成交记录 一买单 一卖单 价格数量总价格
            $nowPrice = $closePrice;
            $orderData['currency_id'] = $currencyId;
            $orderData['number']      = $orderNumber;
            $orderData['price']       = $nowPrice;
            $orderData['total_price'] = $orderNumber * $nowPrice;
            $orderData['create_at']  = \Util\common::getDataTime();
            $orderData['update_at']  = \Util\common::getDataTime();
            $orderData['pay_at']     = \Util\common::getDataTime();
            //根据谁的数量多谁的为卖单
            if($admin1Number['number'] >= $admin2Number['number']){ //admin1为卖单
                //生成admin1的交易记录 卖出
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 2;
                $admin1Data['procedures'] = $admin1Data['total_price'] * 0.1;
                $admin1Data['total_price'] = $admin1Data['total_price'] - $admin1Data['procedures'];
                //生成admin2的交易记录 买入
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin1的数量
                $this->reduceNumber($admin1['id'],$currencyId,$orderNumber);
                //增加admin1的余额 （减手续费）
                $this->plusPrice($admin1['id'],$orderNumber * $nowPrice);
                //增加admin2的数量
                $this->plusNumber($admin2['id'],$currencyId,$orderNumber);
                //减去admin2的余额
                $this->reducePrice($admin2['id'],$admin2Data['total_price']);
            }else{ //admin2为卖单
                //生成admin2的交易记录 卖出
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 2;
                $admin2Data['procedures'] = $admin2Data['total_price'] * 0.1;
                $admin2Data['total_price'] = $admin2Data['total_price'] - $admin2Data['procedures'];
                //生成admin1的交易记录 买入
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin2的数量
                $this->reduceNumber($admin2['id'],$currencyId,$orderNumber);
                //增加admin2的余额 （减手续费）
                $this->plusPrice($admin2['id'],$orderNumber * $nowPrice);
                //增加admin1的数量
                $this->plusNumber($admin1['id'],$currencyId,$orderNumber);
                //减去admin1的余额
                $this->reducePrice($admin1['id'],$admin1Data['total_price']);
            }
        }
    }
    public function check9Action(){
        sleep(14);
        //检查当日价格是否有最高价和最低价出现，如果没有，则将最接近最高价的数据记录改成最高价格，最接近最低价的数据记录改成最低价，并生成一条17:00的收盘成交价数据，价格为收盘价，数量按照上面的规则即可
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 9;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            $openPrice = $shellInfo['open_price'];  //开价
            $closePrice = $shellInfo['close_price'];  //关价
            $maxPrice = $shellInfo['max_price'];  //最高
            $minPrice = $shellInfo['min_price'];  //最低
            //查询最高价
            $whereMax['currency_id'] = $currencyId;
            $whereMax['user_id'] = 9;
            $whereMax['pay_at'] = ['between',[$dateTime." 00:00:01",$dateTime." 23:59:59"]];
            $order = 'price desc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最高价的话讲最接近对最高价的改为最该嫁
            if($item['price'] != $maxPrice){
                $data['price'] = $maxPrice;
                $data['total_price'] = $item['number'] * $maxPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //检查最低价
            $order = 'price asc,pay_at desc';
            $item = ExOrder::findRow($whereMax,null,$order);
            $item = $item->toArray();
            //没有最低价的话将接近最低价的改为最低价
            if($item['price'] != $minPrice){
                $data['price'] = $minPrice;
                $data['total_price'] = $item['number'] * $minPrice;
                $data['update_at'] = \Util\common::getDataTime();
            }
            //生成一条交易记录 价格为收盘价 数量根据 10～5000随机整数
            $orderNumber = rand(10,5000);
            $admin1  = ExUsers::itemById(9); //账号1
            $admin2  = ExUsers::itemById(10); //账号2
            //两个账号币种的数量
            $admin1Number = ExExchangeRecord::itemsByUserIdAndType(9,$currencyId);
            $admin2Number = ExExchangeRecord::itemsByUserIdAndType(10,$currencyId);
            //先生成一条成交记录 一买单 一卖单 价格数量总价格
            $nowPrice = $closePrice;
            $orderData['currency_id'] = $currencyId;
            $orderData['number']      = $orderNumber;
            $orderData['price']       = $nowPrice;
            $orderData['total_price'] = $orderNumber * $nowPrice;
            $orderData['create_at']  = \Util\common::getDataTime();
            $orderData['update_at']  = \Util\common::getDataTime();
            $orderData['pay_at']     = \Util\common::getDataTime();
            //根据谁的数量多谁的为卖单
            if($admin1Number['number'] >= $admin2Number['number']){ //admin1为卖单
                //生成admin1的交易记录 卖出
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 2;
                $admin1Data['procedures'] = $admin1Data['total_price'] * 0.1;
                $admin1Data['total_price'] = $admin1Data['total_price'] - $admin1Data['procedures'];
                //生成admin2的交易记录 买入
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin1的数量
                $this->reduceNumber($admin1['id'],$currencyId,$orderNumber);
                //增加admin1的余额 （减手续费）
                $this->plusPrice($admin1['id'],$orderNumber * $nowPrice);
                //增加admin2的数量
                $this->plusNumber($admin2['id'],$currencyId,$orderNumber);
                //减去admin2的余额
                $this->reducePrice($admin2['id'],$admin2Data['total_price']);
            }else{ //admin2为卖单
                //生成admin2的交易记录 卖出
                $admin2Data = $orderData;
                $admin2Data['user_id']    = $admin1['id'];
                $admin2Data['type']       = 2;
                $admin2Data['procedures'] = $admin2Data['total_price'] * 0.1;
                $admin2Data['total_price'] = $admin2Data['total_price'] - $admin2Data['procedures'];
                //生成admin1的交易记录 买入
                $admin1Data = $orderData;
                $admin1Data['user_id']    = $admin1['id'];
                $admin1Data['type']       = 1;
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin1Data);
                unset($obj);
                $obj = new ExOrder();
                ExOrder::addData($obj,$admin2Data);
                //减去admin2的数量
                $this->reduceNumber($admin2['id'],$currencyId,$orderNumber);
                //增加admin2的余额 （减手续费）
                $this->plusPrice($admin2['id'],$orderNumber * $nowPrice);
                //增加admin1的数量
                $this->plusNumber($admin1['id'],$currencyId,$orderNumber);
                //减去admin1的余额
                $this->reducePrice($admin1['id'],$admin1Data['total_price']);
            }
        }
    }
    
    
    public function khistoryAction(){
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 1;
        $whereShell['currency_id'] = $currencyId;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            if(!empty($shellInfo)){
                $currency = ExCurrency::find('id = '.$currencyId);
                $currency = $currency->toArray();
                foreach ($currency as $item) {
                    //计算今天的最高价 最低价 开盘价 收盘价 和时间戳
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
AND o.currency_id = '.$item['id'].'
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
                    $kdata['currency_id'] = $item['id'];
                    $kdata['max_price'] = $items['max'];
                    $kdata['min_price'] = $items['min'];
                    $kdata['date'] = $date;
                    $kdata['create_at'] = \Util\common::getDataTime();
                    $kdata['total_number'] = $items['total_number'];
                    $kdata['open_price'] = $shellInfo['open_price'];
                    $kdata['close_price'] = $shellInfo['close_price'];
                    unset($obj);
                    $obj = new ExKHistory();
                    ExKHistory::addData($obj,$kdata);
                }
            }
        }
    }
    public function khistory4Action(){
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 4;
        $whereShell['currency_id'] = $currencyId;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            if(!empty($shellInfo)){
                $currency = ExCurrency::find('id = '.$currencyId);
                $currency = $currency->toArray();
                foreach ($currency as $item) {
                    //计算今天的最高价 最低价 开盘价 收盘价 和时间戳
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
AND o.currency_id = '.$item['id'].'
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
                    $kdata['currency_id'] = $item['id'];
                    $kdata['max_price'] = $items['max'];
                    $kdata['min_price'] = $items['min'];
                    $kdata['date'] = $date;
                    $kdata['create_at'] = \Util\common::getDataTime();
                    $kdata['total_number'] = $items['total_number'];
                    $kdata['open_price'] = $shellInfo['open_price'];
                    $kdata['close_price'] = $shellInfo['close_price'];
                    unset($obj);
                    $obj = new ExKHistory();
                    ExKHistory::addData($obj,$kdata);
                }
            }
        }
    }
    public function khistory6Action(){
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 6;
        $whereShell['currency_id'] = $currencyId;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            if(!empty($shellInfo)){
                $currency = ExCurrency::find('id = '.$currencyId);
                $currency = $currency->toArray();
                foreach ($currency as $item) {
                    //计算今天的最高价 最低价 开盘价 收盘价 和时间戳
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
AND o.currency_id = '.$item['id'].'
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
                    $kdata['currency_id'] = $item['id'];
                    $kdata['max_price'] = $items['max'];
                    $kdata['min_price'] = $items['min'];
                    $kdata['date'] = $date;
                    $kdata['create_at'] = \Util\common::getDataTime();
                    $kdata['total_number'] = $items['total_number'];
                    $kdata['open_price'] = $shellInfo['open_price'];
                    $kdata['close_price'] = $shellInfo['close_price'];
                    unset($obj);
                    $obj = new ExKHistory();
                    ExKHistory::addData($obj,$kdata);
                }
            }
        }
    }
    public function khistory7Action(){
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 7;
        $whereShell['currency_id'] = $currencyId;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            if(!empty($shellInfo)){
                $currency = ExCurrency::find('id = '.$currencyId);
                $currency = $currency->toArray();
                foreach ($currency as $item) {
                    //计算今天的最高价 最低价 开盘价 收盘价 和时间戳
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
AND o.currency_id = '.$item['id'].'
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
                    $kdata['currency_id'] = $item['id'];
                    $kdata['max_price'] = $items['max'];
                    $kdata['min_price'] = $items['min'];
                    $kdata['date'] = $date;
                    $kdata['create_at'] = \Util\common::getDataTime();
                    $kdata['total_number'] = $items['total_number'];
                    $kdata['open_price'] = $shellInfo['open_price'];
                    $kdata['close_price'] = $shellInfo['close_price'];
                    unset($obj);
                    $obj = new ExKHistory();
                    ExKHistory::addData($obj,$kdata);
                }
            }
        }
    }
    public function khistory9Action(){
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = 9;
        $whereShell['currency_id'] = $currencyId;
        $shellInfo = ExShell::findRow($whereShell);
        if($shellInfo) {
            $shellInfo = $shellInfo->toArray();
            if(!empty($shellInfo)){
                $currency = ExCurrency::find('id = '.$currencyId);
                $currency = $currency->toArray();
                foreach ($currency as $item) {
                    //计算今天的最高价 最低价 开盘价 收盘价 和时间戳
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
AND o.currency_id = '.$item['id'].'
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
                    $kdata['currency_id'] = $item['id'];
                    $kdata['max_price'] = $items['max'];
                    $kdata['min_price'] = $items['min'];
                    $kdata['date'] = $date;
                    $kdata['create_at'] = \Util\common::getDataTime();
                    $kdata['total_number'] = $items['total_number'];
                    $kdata['open_price'] = $shellInfo['open_price'];
                    $kdata['close_price'] = $shellInfo['close_price'];
                    unset($obj);
                    $obj = new ExKHistory();
                    ExKHistory::addData($obj,$kdata);
                }
            }
        }
    }
    
    
    public function khistoryOtherAction(){
        $dateTime   = \Util\common::getDate(); //当天日期
        $whereShell['date'] = $dateTime;
        $currencyId = [2,3,5,8,10,11,12,13,14,15];
        foreach ($currencyId as $id){
            $initPrice = ExCurrency::findRow(array('id'=>$id));
            $initPrice = $initPrice->toArray();
            $kdata['currency_id'] = $initPrice['id'];
            $kdata['max_price'] = $initPrice['init_price'];
            $kdata['min_price'] = $initPrice['init_price'];
            $kdata['date'] = \Util\common::getDate();
            $kdata['create_at'] = \Util\common::getDataTime();
            $kdata['total_number'] = 0;
            $kdata['open_price'] = $initPrice['init_price'];
            $kdata['close_price'] = $initPrice['init_price'];
            unset($obj);
            $obj = new ExKHistory();
            ExKHistory::addData($obj,$kdata);
        }
    }
    
    
    /**
     * 减少币种余额
     * @param $userId
     * @param $currencyId
     * @param $number
     */
    public function reduceNumber($userId,$currencyId,$number){
        $where['user_id'] = $userId;
        $where['currency_id'] = $currencyId;
        $item = ExExchangeRecord::itemsByUserIdAndType($userId,$currencyId);
        $data['number'] = $item['number'] - $number;
        ExExchangeRecord::updataData($where,$data);
    }
    
    /**
     * 增加币种余额
     * @param $userId
     * @param $currencyId
     * @param $number
     */
    public function plusNumber($userId,$currencyId,$number){
        $myType = ExExchangeRecord::itemsByUserIdAndType($userId,$currencyId);
        if(empty($myType)){
            unset($typeObj);
            $dataType['user_id'] = $userId;
            $dataType['currency_id'] = $currencyId;
            $dataType['number'] = $number;
            $dataType['create_at'] = \Util\common::getDataTime();
            $dataType['update_at'] = \Util\common::getDataTime();
            $typeObj = new ExExchangeRecord();
            ExExchangeRecord::addData($typeObj,$dataType);
        }else{
            $typeWhere['user_id'] = $userId;
            $typeWhere['currency_id'] = $currencyId;
            $dataType['number'] = $myType['number'] + $number;
            $dataType['update_at'] = \Util\common::getDataTime();
            ExExchangeRecord::updataData($typeWhere,$dataType);
        }
    }
    
    /**
     * 减余额
     * @param $userId
     * @param $price
     */
    public function reducePrice($userId,$price){
        $user = ExUsers::itemById($userId);
        $dataUser['balance'] = $user['balance'] - $price;
        ExUsers::saveDataById($userId,$dataUser);
    }
    
    /**
     * 加余额
     * @param $userId
     * @param $price
     */
    public function plusPrice($userId,$price){
        $user = ExUsers::itemById($userId);
        $dataUser['balance'] = $user['balance'] + $price;
        ExUsers::saveDataById($userId,$dataUser);
    }
    
    
    public function saveInit($dateTime,$openPrice,$closePrice,$currencyId){
        $whereInit = ['date'=>$dateTime,'currency_id'=>$currencyId];
        $init = ExInitialization::findRow($whereInit);
        if($init){
            $init = $init->toArray();
            if(!empty($init)){
                $dateInit['open_price']  = $openPrice;
                $dateInit['close_price'] = $closePrice;
                $dateInit['currency_id'] = $currencyId;
                ExInitialization::updataData($whereInit,$dateInit);
            }else{
                unset($obj);
                $obj = new ExInitialization();
                $dateInit['date'] = $dateTime;
                $dateInit['open_price']  = $openPrice;
                $dateInit['close_price'] = $closePrice;
                $dateInit['currency_id'] = $currencyId;
                ExInitialization::addData($obj,$dateInit);
            }
        }else{
            unset($obj);
            $obj = new ExInitialization();
            $dateInit['date'] = $dateTime;
            $dateInit['open_price']  = $openPrice;
            $dateInit['close_price'] = $closePrice;
            $dateInit['currency_id'] = $currencyId;
            ExInitialization::addData($obj,$dateInit);
        }
    }
}