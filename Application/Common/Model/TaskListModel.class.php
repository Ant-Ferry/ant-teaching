<?php
// +----------------------------------------------------------------------
// | Author: 蜉尘 <cheng1483@163.com>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
/**
 * 作业数据模型
 * @author 蜉尘 <cheng1483@163.com>
 */

class TaskListModel extends Model {

    protected $_validate = array(
        array('id', 'require', 'id不能为空','0','','2' ),
        array('id', 'number', 'id必须为数字','0','','2' ),
        array('status', array(-1,0,1), '状态值必须为数字', 2, 'in' ),
        array('sort', 'number', '排序值必须为数字'),

        array('title', 'require', '标题不能为空' ),
        array('content', 'require', '内容不能为空' ),
    );

    protected $_auto = array(
        array('create_time',NOW_TIME,1)
    );
    
    /**
     * [getTaskList 获得作业列表]
     * @param  integer $group  [分组]
     * @param  integer $status [状态]
     * @param  array   $order  [排序]
     * @return [array]          [返回查询结果]
     */
    public function getTaskList($tid ,$order = array('create_time'=>'desc')){
        $data['status'] = 1;
        $data['tid'] = $tid;

        $field = array('id' ,'title','create_time');

        $returnData = $this->selectData($data,$order, $field);
        return $returnData;
    }

    /**
     * [selectData 查询数据]
     * @param  [type]  $data  [查询条件]
     * @param  string  $order [排序方式]
     * @param  boolean $field [要返回的字段]
     * @return [type]         [返回结果]
     */
    public function selectData($data, $order ,$field = true){
        $returnData = $this->create($data);
        if(!$returnData)
           exit($this->getError());
        else {
           $returnData = $this->where($data)->order($order)->field($field)->select();
           //echo $this->_sql();
        }
        return $returnData;
    }
}
