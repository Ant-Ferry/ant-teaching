<?php
namespace Common\Model;
use Think\Model;
//use Think\Page;

/**
 * 广告列表模型
 */

class PlayVideoModel extends Model{

    /* 自动验证规则 */
    protected $_validate = array(
        array('id', 'require', 'id不能为空','0','','2' ),
        array('id', 'number', 'id必须为数字','0','','2' ),
        array('title', 'require', '标题不能为空'),
        array('narrator', 'require', '讲诉人不能为空'),
        );

    /* 自动完成规则 */
    protected $_auto = array(
        array('create_time',NOW_TIME),
        array('status',1,1),
        array('type',1,1),   
    );


}
