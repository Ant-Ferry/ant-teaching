<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace User\Model;
use Think\Model;
/**
 * 会员模型 教员
 */
class UcenterEduModel extends Model{
	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = UC_TABLE_PREFIX;

	/**
	 * 数据库连接
	 * @var string
	 */
	protected $connection = UC_DB_DSN;

	/* 用户模型自动验证 */
	protected $_validate = array(	
		array('member_id','','用户已经是教员！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		//array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
	);

	/* 用户模型自动完成 */
	protected $_auto = array(
		array('status', 'getStatus', self::MODEL_BOTH, 'callback'),
	);

	/**
	 * 根据配置指定用户状态
	 * @return integer 用户状态
	 */
	protected function getStatus(){
		return true; //TODO: 暂不限制，下一个版本完善
	}

	/**
	 * 添加一个教员
	 * @param  string $member_id	用户名id
	 * @param  string $password 	用户密码
	 * @return integer          	添加成功-用户信息，注册失败-错误编号
	 */
	public function addEdu($member_id, $type = '1'){
		$data = array(
			'member_id' => $member_id,
		);

		/* 添加用户 */
		if($this->create($data)){
			$uid = $this->add();
			return $uid ? $uid : 0; //0-未知错误，大于0-添加成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}

	/**
	 * 获得教员id
	 * @param  string  $member_id	用户名id
	 * @return integer           	成功-教员ID，失败-错误编号
	 */
	public function getEduId($member_id){
		$map = array();
		$map['member_id'] = $member_id;

		/* 获取用户数据 */
		$user = $this->where($map)->find();
		if(is_array($user) && $user['status']){
			return $user['id']; //返回教员ID
		} else {
			return -1; //用户不存在教员组中
		}
	}

	/**
	 * 获得教员对应的用户id
	 * @param  string  $member_id	用户名id
	 * @return integer           	成功-教员ID，失败-错误编号
	 */
	public function getEduMemberId($id){
		$map = array();
		$map['id'] = $id;

		/* 获取用户数据 */
		$user = $this->where($map)->find();
		if(is_array($user) && $user['status']){
			return $user['id']; //返回用户ID
		} else {
			return -1; //用户不存在教员组中
		}
	}


	public function deleteEdu($id){
		$map = array();
		$map['id'] = $id;

		$effect = $this->where($map)->delete();

		if($effect != 0){
			return $effect;
		}
		else{
			return 0; //删除失败
		}
	}
/*************************************************************/








}
