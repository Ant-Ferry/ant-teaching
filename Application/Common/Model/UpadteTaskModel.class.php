<?php
// +----------------------------------------------------------------------
// | Author: 蜉尘 <cheng1483@163.com>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
use Think\Upload;

/**
 * 作业模型
 * 负责作业的上传
 */

class UpadteTaskModel extends Model{

	/* 用户模型自动验证 */
	protected $_validate = array(
        array('id', 'require', 'id不能为空','0','','2' ),
        array('id', 'number', 'id必须为数字','0','','2' ),
        
        array('user_name', 'require', '标题不能为空' ),
        array('task_id', 'require', '任务不能为空' ),
        array('file_id', 'require', '上传文件不能为空' ),
    );
   
    protected $_auto = array(
        array('create_time', NOW_TIME, 3),
        array('status',1,1),
    );



}
