<?php
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
class IndexController extends ControllerBase{

    public function indexAction(){
        //获取四种类型
        $items = ExCurrency::find();
        $items = $items->toArray();
        $types = [];
        foreach ($items as $item){
            $value = [];
            //最新价格
            $newPrice = ExOrder::getMaxOrderPrice($item['id']);
            if($newPrice){
                $newPrice = $newPrice->toArray();
            }else{
                $newPrice = [];
            }
            //24小时成交量
            //24小时成交额
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
            $orders = new Resultset(
                null,
                $obj,
                $obj->getReadConnection()->query($sql, null)
            );
            if(!empty($orders)){
                $orders = $orders->toArray();
                $orders = isset($orders[0]) ? $orders[0] : [];
            }else{
                $orders = [];
            }
            if(empty($orders)){
                $orders['total_number'] = 0;
                $orders['total_price'] = 0;
                $orders['open_price'] = 0;
            }
            $value['total_number'] = $orders['total_number'];
            $value['total_price'] = $orders['total_price'];
            $value['new_price'] = isset($newPrice['price']) ? $newPrice['price'] : 0;
            if($value['new_price'] <= 0){
                $value['new_price'] = $item['init_price'];
            }
            $value['name'] = $item['name'];
            //涨幅度
            $value['rise'] = sprintf("%.2f", $value['new_price'] <= 0 || $orders['open_price'] <=  0 ? 0 : ($value['new_price'] - $orders['open_price']) / $orders['open_price'] * 100);;
            //总市值
            $value['total_market'] =  $value['new_price'] * $item['issue_number'];
            $types[] = $value;
        }
        $this->view->setVar('types',$types);
    }
}