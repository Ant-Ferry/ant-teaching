<?php
namespace Common\Model;
use Think\Model;

class MessageInfoModel extends Model
{
	protected $_validate = array(
		array('title','require',"主题不能为空，请填写",self::MODEL_INSERT), // 在新增的时候验证标题不为空
	);
	/* 自动完成规则 */
	protected $_auto = array( 
			array('create_time','getCreateTime',self::MODEL_INSERT,'callback'),
			array('read',1,self::MODEL_INSERT),
			 );

	/* 获取编辑数据 */
	public function detail($id) {
		$data = $this -> find($id);
		return $data;
	}

	/* 删除 */
	public function del($id) {
		return $this -> delete($id);
	}

	/**
	 * 新增或更新一个文档
	 * @return boolean fasle 失败 ， int  成功 返回完整的数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function update() {
		/* 获取数据对象 */
		$data = $this -> create();
		if (empty($data)) {
			return false;
		}
		/* 添加或新增基础内容 */
		if (empty($data['id'])) {//新增数据
			$id = $this -> add();
			//添加基础内容
			if (!$id) {
				$this -> error = '新增留言板出错！';
				return false;
			}
		} else {//更新数据
			$status = $this -> save();
			//更新基础内容
			if (false === $status) {
				$this -> error = '更新留言板出错！';
				return false;
			}
		}
		//内容添加或更新完成
		return $data;
	}

	/* 时间处理规则 */
	protected function getCreateTime() {
		$create_time = I('post.create_time');
		return $create_time ? strtotime($create_time) : NOW_TIME;
	}

}
