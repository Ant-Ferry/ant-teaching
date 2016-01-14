<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use User\Api\UserApi;
use OT\PclZip;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {

	/* 用户中心首页 */
	public function index(){
		
	}

	/* 注册页面 */
	public function register($username = '', $password = '', /*$repassword = '',*/ $email = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 检测密码 */
			/*
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			
			*/
			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				$this->success('注册成功！',U('login'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
			$this->display();
		}
	}

	/* 登录页面 */
	public function login($username = '', $password = '', $verify = ''){
		if(IS_POST){ //登录验证
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($username, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Member = D('Member');
				if($Member->login($uid)){ //登录用户
					$cook = Cookie('__forward__');
					if(isset($cook)){
						//TODO:跳转到登录前页面
						$this->success('登录成功！',Cookie('__forward__'));
					}else{
						//TODO:跳转到登录前页面
						$this->success('登录成功！',U('Index/index'));
					}
					
				} else {
					$this->error($Member->getError());
				}

			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}

		} else { //显示登录表单
			$this->display();
		}
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			$cook = Cookie('__forward__');
			if(isset($cook)){
				//TODO:跳转到登录前页面
				$this->success('退出成功！',Cookie('__forward__'));
			}else{
				//TODO:跳转到登录前页面
				$this->success('退出成功！',U('Index/index'));
			}
			//$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}

	/* 个人信息页 */
	public function info($id='-1'){
		if($uid = is_login()){
			$this->assign('page','info');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			
			if(IS_POST){
	            $Member = D('Member');
	            $data = $Member->create();

	            if($data){
	                if($Member->save($data)){
	                    $this->success('修改成功',U('Home/User/info'));
	                } else {
	                    $this->error('请修改后,再保存');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } else {
	        	$this->userInfoLoad($uid);
	        	if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
				}
	        	$this->display();
	        }
		} else {
			$this->redirect('User/login');

		}
	}


	/*视频详情页面*/
	public function videoDetails(){
		if($uid = is_login()){
			$this->assign('page','videoDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
						
			if(IS_POST){
	            $Member = D('Member');
	            $data = $Member->create();

	            if($data){
	                if($Member->save($data)){
	                    $this->success('修改成功',U('Home/User/info'));
	                } else {
	                    $this->error('请修改后,再保存');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } else {
	        	$this->userInfoLoad($uid);
				
				$map['uid'] = $uid;
				$map['status'] = 1;
				$list = $this->lists('PlayVideo', $map,'create_time desc,id');
				$this->assign('list', $list);
				//获得所有教师
		        $personnes = $this->getPersonnelName();
		        $this -> assign('personnes', $personnes);
				
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
				}

				$this->display();
	        }

		} else {
			$this->redirect('User/login');

		}
	}

	/*视频上传页面*/
	public function videoDetailsUp(){
		if($uid = is_login()){
			$this->assign('page','videoDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
						
			if(IS_POST){
	            $Member = D('Member');
	            $data = $Member->create();

	            if($data){
	                if($Member->save($data)){
	                    $this->success('修改成功',U('Home/User/info'));
	                } else {
	                    $this->error('请修改后,再保存');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } else {
	        	$this->userInfoLoad($uid);
				
				$map['uid'] = $uid;
				$map['status'] = 1;
				
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师id
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);

					$map = array();
					$map['status']  = 1;
					$map['authority']  = $teachValue['tid'];
					$list = $this->lists('Resource', $map,'id');
					$this->assign('list', $list);

					//获得标签
			        $tabs = D('ResourceTab')->getTablists();
			        $this -> assign('tabs', $tabs);
			        //获得标签
			        $this -> assign('tab', $map['tab'] );
				}

				$this->display();
	        }

		} else {
			$this->redirect('User/login');

		}
	}

	/*视频上传页面-添加*/
	public function videoDetailsUpAdd(){
		if($uid = is_login()){
			$this->assign('page','videoDetails');//栏目
	    	$teach = is_eduistrator($uid);
			

			if(IS_POST){
	            $BannerList = D('Resource');
	            $data = $BannerList->create();
	            if($data){
	                if($BannerList->add()){
	                    //S('DB_CONFIG_DATA',null);
	                    $this->success('新增成功', U('Home/User/videoDetailsUp'));
	                } else {
	                    $this->error('新增失败');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } else {
	        	
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师id
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);
					$this -> assign('teachvalue', $teachValue);
					//获得标签
			        $tabs = D('ResourceTab')->getTablists();
			        $this -> assign('tabs', $tabs);
			        
			        $this->display('videoDetailsUpEdit');
				}
				else{
					$this->redirect('User/login');
				}
				
	        }

		} else {
			$this->redirect('Home/User/info');

		}
	}

	/*视频上传页面-编辑*/
	public function videoDetailsUpEdit($id = 0){
		if($uid = is_login()){
			$this->assign('page','videoDetails');//栏目
	    	$teach = is_eduistrator($uid);
			$DataModel = D('Resource');

			if(IS_POST){
	        	
	            $data = $DataModel->create();
	            if($data){
	                if($DataModel->save($data)){

	                    S('DB_CONFIG_DATA',null);//设置缓存
	                    $this->success('更新成功', U('Home/User/videoDetailsUp'));
	                } else {
	                    $this->error('更新失败');
	                }
	            } else {
	                $this->error($DataModel->getError());
	            }

	        } else {
	        	
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					
					$info = array();
		            /* 获取数据 */
		            $info = $DataModel->field(true)->find($id);

		            $authoritytype = D('PersonnelData')->field(true)->find($info['authority']);
		            //$info['authoritytype'] = D('PersonnelData')->field(true)->find($id)['']
		            if(false === $info){
		                $this->error('获取配置信息错误');
		            }else if(false === $authoritytype){
		                $this->error('获取配置信息错误');
		            }
		            else{
		                $info['authoritytype'] = $authoritytype['type'];
		            }

		            $this->assign('info', $info);
					//获得标签
			        $tabs = D('ResourceTab')->getTablists();
			        $this -> assign('tabs', $tabs);
			        
			        $this->display('videoDetailsUpEdit');
				}
				else{
					$this->redirect('User/login');
				}
				
	        }

		} else {
			$this->redirect('Home/User/info');
		}
	}

	/*视频上传页面-删除*/
	public function videoDetailsUpDel(){
		$id = array_unique((array)I('id',0));

        if ( empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Resource')->where($map)->delete()){
            S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('update_config','Resource',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
	}

	/*作业详情页面*/
	public function taskDetails(){
		if($uid = is_login()){
			$this->assign('page','taskDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			
			//获得个人状态：
			if(IS_POST){
	            $Member = D('Member');
	            $data = $Member->create();

	            if($data){
	                if($Member->save($data)){
	                    $this->success('修改成功',U('Home/User/info'));
	                } else {
	                    $this->error('请修改后,再保存');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } else {
	        	$this->userInfoLoad($uid);
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师id
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);
					
					//输出作业布置列表
					$map['tid'] = $teachValue['tid'];
					$map['status'] = 1;
					$list = $this->lists('TaskList', $map,'create_time desc,id');
					$this->assign('list', $list);
				}else{
					//输出作业提交列表
					$map['uid'] = $uid;
					$map['status'] = 1;
					$list = $this->lists('UpdateTask', $map,'create_time desc,id');
					$this->assign('list', $list);
				}
				
				//
				$this->display();
	        }

		} else {
			$this->redirect('User/login');

		}
	}
	/*添加作业*/
	public function taskUpAdd(){
		if(!is_login()) {
			$this->redirect('User/login');
		} 
		else {

			$uid = is_login();
			$this->assign('page','taskDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			
			//获得个人状态：
			if(IS_POST)
			{
				$Media  = null;
				if($teach != -1){
					$Media = D('TaskList');
				}else{
					$Media = D('UpdateTask');
				}
	            $data = $Media->create();

	            if($data){
	                if($Media->add($data)){
	                    //S('DB_CONFIG_DATA',null);
	                    $this->success('新增成功', U('Home/User/taskDetails'));
	                } else {
	                    $this->error('新增失败');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } 
	        else 
	        {
	        	$this->userInfoLoad($uid);
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师信息
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);
					$this->assign('teachValue',$teachValue);//栏目
					
				}else{
					
			        $personnes = $this->getPersonnelName(1);
			        $this -> assign('personnes', $personnes);
				}
				
				$this->display('taskUpEdit');
	        }

		} 
	}
	/*编辑作业*/
	public function taskUpEdit($id = 0){
		if(!is_login()) {
			$this->redirect('User/login');
		} 
		else {

			$uid = is_login();
			$this->assign('page','taskDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			
			//获得个人状态：
			if(IS_POST)
			{
				$Media  = null;
				if($teach != -1){
					$Media = D('TaskList');
				}else{
					$Media = D('UpdateTask');
				}

	            $data = $Media->create();

	            if($data){
	                if($Media->save($data)){
	                    //S('DB_CONFIG_DATA',null);
	                    $this->success('更新成功', U('Home/User/taskDetails'));
	                } else {
	                    $this->error('更新失败');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } 
	        else 
	        {
	        	$this->userInfoLoad($uid);
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师信息
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);
					$this->assign('teachValue',$teachValue);//栏目
					//输出作业布置列表
					$task = D('TaskList');
					$info = $task->where(array('id' =>$id , 'status' =>1))->find();
					$this->assign('info',$info);//栏目

				}else{
					//获得所有教师
			        $personnes = $this->getPersonnelName(1);
			        $this -> assign('personnes', $personnes);
					
					//获得对应的提交信息
					$Media = D('UpdateTask');
					$map = array();
					$map['id'] = $id;
					$map['uid'] = $uid;
					$map['status'] = 1;
					$info = $Media->where($map)->find();

					//获得对应的的task信息
					if(isset($info)){
						$Media = D('TaskList');
						$map = array();
						$map['id'] = $info['task_id'];
						$map['status'] = 1;
						$task = $Media->where($map)->find();

						$info['teach'] = $task['tid'];

						$map = array();
						$map['tid'] = $task['tid'];
						$map['status'] = 1;
						//获得对应的任务列表
						$taskList = $Media->where($map)->select();
						$this->assign('tasklist',$taskList);//栏目

					}

					$this->assign('info',$info);//栏目
					
				}
				$this->display('taskUpEdit');
	        }

		} 
	}

	/*删除作业*/
	public function taskUpDel(){
		if(!is_login()) {
			$this->redirect('User/login');
		} 
		else {

			$id = array_unique((array)I('id',0));
			if ( empty($id)) {
	            $this->error('请选择要操作的数据!');
	        }
	        $map = array('id' => array('in', $id) );

			$uid = is_login();
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			
			$Media  = null;
			if($teach != -1){
				$Media = D('TaskList');
			}else{
				$Media = D('UpdateTask');
			}

	        if($Media->where($map)->delete()){
	            //S('DB_CONFIG_DATA',null);
	            $this->success('删除成功');
	        } else {
	            $this->error('删除失败！');
	        }

		} 
	}

	public function taskBale($id = 0){
		if($uid = is_login()){
			$this->assign('page','taskDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			
			//获得个人状态：
			if(IS_POST){
	            $Member = D('Member');
	            $data = $Member->create();

	            if($data){
	                if($Member->save($data)){
	                    $this->success('修改成功',U('Home/User/info'));
	                } else {
	                    $this->error('请修改后,再保存');
	                }
	            } else {
	                $this->error($BannerList->getError());
	            }
	        } else {
	        	$this->userInfoLoad($uid);
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师id
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);
					
					//输出作业布置列表
					
					$map['task_id'] = $id;
					$map['status'] = 1;
					$list = $this->lists('UpdateTask', $map,'create_time desc,id');
					$this->assign('list', $list);
				}else{
					$this->error('404');
					//输出作业提交列表
					/*
					$map['uid'] = $uid;
					$map['status'] = 1;
					$list = $this->lists('UpdateTask', $map,'create_time desc,id');
					$this->assign('list', $list);
					*/
				}
				
				//
				$this->display();
	        }

		} else {
			$this->redirect('User/login');

		}
	}

	public function taskBaleZip($id = 0){
		if($uid = is_login()){
			$this->assign('page','taskDetails');//栏目
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			{
	        	$this->userInfoLoad($uid);
				if($teach != -1){
					//如果是则返回
					$this->assign('teach',$teach);//栏目
					//获得对应的教师id
					$edu = D('UcenterEdu');
					$teachValue = $edu->getEduMember($teach);
					
					//输出作业布置列表
					
					$map['task_id'] = $id;
					$map['status'] = 1;
					//$list = $this->lists('UpdateTask', $map,'create_time desc,id');
					$list = D('UpdateTask')->where($map)->getField('file_id',true);
					//$this->assign('list', $list);
					$baleFile = array();
					


					foreach ($list as $key => $value) {
						$info = D('FileTask')->where( array('id' => $value))->find();
						$patch = array();
						$patch[79001] = '.'.$info['path'];
						$patch[79003] = $info['name'];
						$baleFile[] = $patch;
					}
					$zipName = 'task_'.time().$id.'.zip';
					$zipurl = C('PCLZIP_TEMPORARY_DIR').$zipName;
					 //实例化类库
					//$zip = new PclZip($zipurl);
					$zip = new PclZip();
					$zip->PclZip($zipurl);

					//var_dump($baleFile);
					$status = $zip->create($baleFile);
					if($status == 0){
						$this->error('打包失败！');
					}else{

						//清除多余的文件
						$time = C('PCLZIP_TIME')? C('PCLZIP_TIME'):3600;
						$dir  = C('PCLZIP_TEMPORARY_DIR');
						$files1 = scandir($dir);
						foreach ($files1 as $key => $value) {
							if(filectime ($dir.$value) < (time()-$time)){
								unlink($dir.$value);
							}
						}
						
						$filename = $zipurl; 
						//文件的类型 
						header('Content-type: application/zip'); 
						//下载显示的名字 
						header('Content-Disposition: attachment; filename="'.$zipName.'"'); 
						readfile($zipName); 


						exit(); 
						//
						
					}

					
					
				}else{
					$this->error('404');
				}
	        }

		} else {
			$this->redirect('User/login');

		}
	}

	/*获得作业对应的列表*/
	public function getTaskList(){
		$tid = I('post.tid','0','htmlspecialchars');
		//输出作业布置列表
		$task = D('TaskList');
		$info = $task->where(array('tid' =>$tid , 'status' =>1))->select();
		$this->ajaxReturn($info);
	}

	/*问答页面*/
	public function letterDetails(){
		if($uid = is_login()){
			$this->assign('page','letterDetails');//栏目
			//当前页面状态
			if(IS_POST){
	            
	        } else {
	        	$this->userInfoLoad($uid);
				
				//判断是否为教员
		    	$teach = is_eduistrator($uid);
				if($teach != -1)
					$this->assign('teach',$teach);//栏目

				//获得收到的私信
				$map = array();
				$map['to'] = $uid;
				$map['type'] = 2; //1：留言  2：私信

				$list = $this->lists('MessageInfo', $map,'create_time desc');
				//var_dump($list);
				$this->assign('list', $list);


				$this->display();
	        }
		} else {
			$this->redirect('User/login');

		}
	}

	/*问答页面*/
	public function letterSet(){
		if($uid = is_login()){
			$this->assign('page','letterDetails');//栏目
			//当前页面状态
			if(IS_POST){
	       	
	        } else {
	        	$this->userInfoLoad($uid);
				//判断是否为教员
		    	$teach = is_eduistrator($uid);
				if($teach != -1)
					$this->assign('teach',$teach);//栏目

				//获得发送的私信
				$map = array();
				$map['from'] = $uid;
				$map['type'] = 2; //1：留言  2：私信

				$list = $this->lists('MessageInfo', $map,'create_time desc');
				//var_dump($list);
				$this->assign('list', $list);

				$this->display();
	        }
		} else {
			$this->redirect('User/login');

		}
	}



	/**
	 *显示私信内容 
	 */
	public function letterShow($id="0"){
		if($uid = is_login()){
			$this->assign('page','letterDetails');//栏目
			
        	$this->userInfoLoad($uid);
			//判断是否为教员
	    	$teach = is_eduistrator($uid);
			if($teach != -1)
				$this->assign('teach',$teach);//栏目

			$info = D('MessageInfo')->where(array('status' =>1 ))->getById($id);

			if(isset($info)){
				if($info['read']==1 && $info['to']==$uid){
					$data = array();
					$data['id'] = $info['id'];
					$data['read'] = 0;
					D('MessageInfo')->save($data);
				}
				$this->assign('info',$info);

			}else{
				$this->error('404');
			}
				

			$this->display();
	        
		} else {
			$this->redirect('User/login');

		}
	}
	/**
	 *发送私信
	 */

	/**
	 * [letterEdit 信件编辑<添加>]
	 * @param  [type] $id [用户id]
	 * @return [type]     [description]
	 */
	public function letterEdit($id = null){
		if($uid = is_login()){
			$this->assign('page','letterDetails');//栏目
			//当前页面状态
			if(IS_POST){
	            $Member = D('MessageInfo');
	            $data = $Member->create();

	            if($data){
	                if($Member->add($data)){
	                    $this->success('发送成功',U('Home/User/info'));
	                } else {
	                    $this->error('发送失败');
	                }
	            } else {
	                $this->error($Member->getError());
	            }
	        } else {
	        	if(!isset($id)){
	        		$this->error('404');
	        		return;
	        	}else{
	        	  //验证收件人ID合法性
	        	  $receiveInfo = D('Member')->where(array('uid' => $id))->find();
	        	  if(!isset($receiveInfo)){
	        	  	$this->error('4042');
	        		return;
	        	  }
	        	  //var_dump($receiveInfo);
	        	  $this->assign('receiveInfo', $receiveInfo);
				  //验证收件人是否为教师
	    		  $receiTeach = is_eduistrator($id);
	    		  if($receiTeach != -1){
	    		  	$this->assign('receiTeach', $receiTeach);
	    		  }

	    		  //验证发件人是否为教师
	    		  $sendTeach = is_eduistrator($uid);
	    		  if($receiTeach != -1){
	    		  	$this->assign('sendTeach', $sendTeach);
	    		  }

	    		  $info = array();
	    		  $info['from'] = $uid;
	    		  $info['to'] = $id;
	    		  $info['type'] = 2;

	    		  $this->assign('info', $info);

	        	}

				$this->display();
	        }
		} else {
			$this->redirect('User/login');

		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}


    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
        if ( IS_POST ) {
        	/* 检测验证码 */
			if(!check_verify(I('post.verify'))){
				$this->error('验证码输入错误！');
			}

            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');


            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                //$this->success('修改密码成功！',);
                $this->success('修改密码成功！',U('User/info'),3);
                //$this->redirect('修改密码成功！',U('User/info'),3);
            }else{
                $this->error($res['info']);
            }
        }else{
            $this->display();
        }
    }

    public function modifyAvator(){
    	if($uid = is_login()){
			if ( IS_POST ) {
				 $avator   =   I('post.avator/d',0);
				 $map = array('uid' => $uid);
				 if($avator==0){
				 	return null;
				 }
				 else{
				 	//$map['avator'] = NULL;
				 	$map['avator'] =  I('post.avator/d',0);
				 	$Member = D('Member');
	            	
	            	if($Member->save($map)){
	                    
	                    $this->success();
	                } else {
	                    $this->error('请修改后,再保存');
	                }
	            
				 }
			}
		}
		else{
			$this->error('非法操作，请先登录');
			//echo "未登录456";
		}
    }

    public function randomAvator(){
    	if($uid = is_login()){

    		$map['status'] = 1;
    		$map['type'] = 0;
    		$Avator = D('Avator');
    		$random = $Avator->where($map)->getField('id',true);

    		if($random){
    			//echo var_export($random,true);
    			$data["avator"] = $random[array_rand($random, 1)];
				$data['uid'] = $uid;
				$Member = D('Member');
				if($Member->save($data)){
	                    //$this->success('修改成功');
	                   // echo "成功11";
	                   echo get_avator($data["avator"],'path');
	                } else {
	                    //$this->error('请修改后,再保存');
	                    //echo "失败00";
	                }

    		}
    		else{
			$this->error('修改失败');
    		}
    	}
    	
    }

    public function userInfoLoad($uid){
		
    	$map = array('uid' =>$uid ,'status' =>1 );
		//获得个人信息：头像，名字，用户类型
		$field = array('uid','nickname','sex','avator','aboutme','score','login','reg_ip','reg_time','last_login_ip','last_login_time');
		$info = D('Member')->where($map)->field($field)->find();
		//返回个人状态
		$this->assign('userinfo',$info);
    }

    protected function getPersonnelName($type = -1){
       $list = D('PersonnelData')->getPersonnels($type);

       $returnData = array();
       foreach ($list as $key => $value) {
            $returnData[$value['id']] = $value;
       }

       return $returnData;
    }


    public function showDir( $filedir ) {  
		//打开目录  
		$dir = @ dir($filedir);  
		//列出目录中的文件  
		while (($file = $dir->read())!==false)  
		//while(($file = readdir($dir)) !== false)  
		  {  
		     if(is_dir($filedir."/".$file) AND ($file!=".") AND ($file!="..")) {  
		        // echo "dirname: ".$file."<br />";  
		           showDir($filedir."/".$file);  
		      } else {  
		          echo "filename: " .$filedir."/".$file . "<br />";  
		      }  
		 }  
		$dir->close();  
	  
	}  
}
