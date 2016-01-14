<?php
namespace Home\Controller;
use OT\DataDictionary;

class DisplayController extends HomeController {
	
	//设置页面特征：标题，控制器名称，
	public $PageFeature = array('ControllerName' =>'Display/index', 'Title' =>'学生作品');
	 //前置操作方法
    public function _before_index(){
        //在前置方法中 操作判断用户
		$this->assign('PageFeature',$this->PageFeature);
    }

    public function index(){

    	$banner = $this->getBanner($this->PageFeature['ControllerName']);
		$this->assign('banner',$banner);

		$introduction = $this->getIntroduction();
		$this->assign('introduction',$introduction);
		/*
		$displayWork = $this->getDisplayWork();
		$this->assign('displayWork',$displayWork);
		*/
		$map = array();
		$map['status'] = 1;
        $map['type'] = 3;
        $map['group'] = 4;
        $list = $this->lists('MediaData', $map,'id',true,6);
		//$this->assign('list', $list);
		$this->assign('displayWork',$list);

		Cookie('__forward__',$_SERVER['REQUEST_URI']);
    	$this->display();
    }

	protected function getIntroduction(){
		$introduction = array("title"=>C('DISPLAY_TITLE') ,"titleSmall"=>C('DISPLAY_TITLE_SMALL'),"content"=>C('DISPLAY_CONTENT'),"link"=>C('DISPLAY_LINK') );
		return $introduction;
	}

	protected function getDisplayWork(){
		
		$MediaData = D('MediaData');
		$displayWork = $MediaData->getPicturesShow(4);
		return $displayWork;
	}


}