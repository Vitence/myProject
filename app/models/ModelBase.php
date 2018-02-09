<?php
/**
 * Created by PhpStorm.
 * User: freya
 * Date: 15/12/2
 * Time: 14:56
 */
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
class ModelBase extends \Phalcon\Mvc\Model
{
	public function initialize(){
        parent::setup(array(
                'notNullValidations' => false
                )
        );

	}
    public static function txManager(){
        $txManager = new TxManager();
        return $txManager;
    }

    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getModelName() {
        if(empty($this->name)){
            $name = substr(get_class($this),0);
            if ( $pos = strrpos($name,'\\') ) {//有命名空间
                $this->name = substr($name,$pos+1);
            }else{
                $this->name = $name;
            }
        }
        return $this->name;
    }


    /**
     * 拼接where条件
     * @param array $wheres
     * @return array
     * //how to use//
     * $whereMap['id'] = 124222;
     * $whereMap['status'] = array('>',1);
     * $where['interest_type'] = array('between','-1,0'); $where['interest_type'] =array('between',array(-1,0));
     * $where['saving_type'] = array('in','-1,0');$where['saving_type'] =array('in',array(-1,0));
     * $whereMap['_complex'] = array(
        '_logic'=>'or',
        'name'=>'robot',
        'sex'=>'man',
        );
     * //return //
     * condition  = ( ( name = :name:) OR ( sex = :sex:) ) AND ( ( id = :id:) AND ( status > :status:) )
     * parameters = Array ( [name] => robot [sex] => man [id] => 124222 [status] => 1 )
     */
    public static function getWhere($wheres = array()){
        $whereCondition = $whereParam = $whereMap = array();

        //拼接复合查询
        if(array_key_exists('_complex',$wheres)){
            $whereMap[] = $wheres['_complex'] ;
            unset($wheres['_complex']);
        }

        //拼接基础查询
        $whereMap[] = $wheres;

        //解析查询语句
        foreach ($whereMap as $map) {
            if(count($map)>0){
                list($conditions,$parameters)= self::parseWhereItem($map);
                $whereCondition[] = $conditions;
                $whereParam = array_merge($whereParam,$parameters);
            }
        }
        $whereStr = implode(' AND ',$whereCondition);
        return array($whereStr,$whereParam);
    }

    //解析逻辑操作符
    private static function parseOperateItem(&$map){
        $operate  = isset($map['_logic'])?strtoupper($map['_logic']):'';
            if(in_array($operate,array('AND','OR','XOR'))){
                // 定义逻辑运算规则 例如 OR XOR AND NOT
                $operate    =   ' '.$operate.' ';
                unset($map['_logic']);
            }else{
            // 默认进行 AND 运算
            $operate    =   ' AND ';
        }
        return $operate;
    }

    //解析查询语句，返回参数绑定形式的数据
    private static function parseWhereItem($map){
        $conditions = '';
        $parameters = array();
        $operate = self::parseOperateItem($map);
        foreach ($map as $key=>$val) {
            $hasAlias = strpos($key,'.');//字段带别名
            if($hasAlias > 0){
                $keyValue = substr($key,$hasAlias+1);
            }else{
                $keyValue = $key;
            }
            if(is_array($val)){
                switch(strtoupper($val[0])){
                    case 'BETWEEN':
                    case 'NOT BETWEEN':
                        $valArr = is_array($val[1]) ? $val[1] : explode(',',$val[1]);
                        foreach ($valArr as $index=>$v) {
                            $keyArr[] = $keyValue.$index;
                            $parameters[$keyValue.$index] = $valArr[$index];
                        };
                        $keyValue = ' :'.implode(': AND :',$keyArr).':';
                        break;
                    case 'IN':
                    case 'NOT IN':
                        $valArr = is_array($val[1]) ? $val[1] : explode(',',$val[1]);
                        foreach ($valArr as $index=>$v) {
                            $keyArr[] = $keyValue.$index;
                            $parameters[$keyValue.$index] = $valArr[$index];
                        };
                        $keyValue = ' (:'.implode(':,:',$keyArr).':)';
                        break;
                    default:
                        $parameters[$keyValue] = $val[1];
                        $keyValue = ' (:'.$keyValue.':)';
                        break;
                }
                $conditions .= ' ( '.$key.' '.$val[0] . $keyValue . ') '.$operate;
            }else{
                $conditions .= ' ( '.$key.' = :'.$keyValue.':) '.$operate;
                $v = $val;
                $parameters[$keyValue] = $v;
            }

        }
        $conditions = ' ( '.rtrim($conditions,$operate).' ) ';
        return array($conditions,$parameters);
    }

    /**
     * 查询一条数据
     * @param $where
     * @param string $columns
     * @return \Phalcon\Mvc\Model
     */
    public  static function findRow($where,$columns= null,$order =null){
        list($conditions,$parameters) = self::getWhere($where);
        $ret = self::findFirst(array(
            $conditions,
            'bind'  => $parameters,
            'columns' => $columns,
            'order' => $order,
        ));
        return $ret;
    }

    /**
     * 查询多条数据
     * @param $where
     * @param string $columns
     * @return \Phalcon\Mvc\Model
     */
    public static function select($where,$columns= null,$order =null,$limit=null){
        list($conditions,$parameters) = self::getWhere($where);
        $ret = self::find(array(
            $conditions,
            'bind'  => $parameters,
            'columns' => $columns,
            'order' => $order,
            'limit' => $limit,
        ));
        return $ret;
    }

    /**
     * 更新数据
     * @param $where
     * @param $data
     * @param string $columns
     * @return bool
     */
    public  static function updataData($where,$data,$columns= null){
        list($conditions,$parameters) = self::getWhere($where);
        $ret = self::findFirst(array(
            $conditions,
            'bind'  => $parameters,
            'columns' => $columns,
        ));
        return $ret->save($data);
    }
    /**
     * 批量更新数据
     * @param $where
     * @param $data
     * @param string $columns
     * @return bool
     */
    public static function updataAllData($where, $data, $columns = null) {
        list($conditions, $parameters) = self::getWhere($where);
        $ret = self::find(array(
            $conditions,
            'bind' => $parameters,
            'columns' => $columns,
        ));
        return $ret->update($data);
    }

    /**
     * 统计数据条数
     * @param $where
     * @param null $columns
     * @return mixed
     */
    public static function countData($where,$columns= null){
        list($conditions,$parameters) = self::getWhere($where);
        $ret = self::count(array(
            $conditions,
            'bind'  => $parameters,
            'columns' => $columns,
        ));
        return $ret;
    }

    /**
     * 添加数据
     * @param $obj 实例化对象
     * @param $data 添加的数据,key的值与数据库字段值保持一致
     * @return mixed 1:添加成功 0:添加失败
     */
    public static function addData($obj,$data){
        foreach($data as $key => $value){
            $obj->$key = $value;
        }
        return $obj->save();

    }

    /**
     * 计算某个字段的和值
     * @param null $columns 字段名
     * @param null $where   条件
     * @return mixed
     */
    public static function sumData($columns=null,$where=null){
        list($conditions,$parameters) = self::getWhere($where);
        $ret = self::sum(array(
            $conditions,
            'bind'  => $parameters,
            'column' =>$columns,
        ));
        return $ret;
    }

    /**
     * 获取构建器
     * @return QueryBuilder
     */
    public static function getQueryBuilder(){
        return new QueryBuilder();
    }
    
}

?>
