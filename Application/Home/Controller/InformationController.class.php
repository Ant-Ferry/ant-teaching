<?php
namespace Home\Controller;
use OT\DataDictionary;

class InformationController extends HomeController {
	
	//设置页面特征：标题，控制器名称，
    public $PageFeature = array('ControllerName' =>'Information/index', 'Title' =>'前沿专题');
    //前置操作方法
    public function _before_index(){
        //在前置方法中 操作判断用户
        $this->assign('PageFeature',$this->PageFeature);
    }

    public function index(){

   		$banner = $this->getBanner($this->PageFeature['ControllerName']);
  		$this->assign('banner',$banner);

  		$note = $this->getNote();
  		$this->assign('note',$note);
      //获得经典视频
  		$classicalVideo = $this->getClassicalVideo();
  		$this->assign('classicalVideo',$classicalVideo);
      //获得成功人士
  		$successPeople = $this->getSuccessPeople();
  		$this->assign('successPeople',$successPeople);
      //获得领域专家
  		$fieldExpert = $this->getFieldExpert();
  		$this->assign('fieldExpert',$fieldExpert);
      //获得优秀教师
      $goodTeach = $this->getGoodTeach();
      $this->assign('goodTeach',$goodTeach);
      //获得热门资讯
  		$hotTopics = $this->getHotTopics();
  		$this->assign('hotTopics',$hotTopics);
			
      Cookie('__forward__',$_SERVER['REQUEST_URI']);
    	$this->display();
    }

  

    public function getNote(){
    	$note = array ("img"=>"/page/information/keyword.jpg","title"=>"图片1");
	    return $note;
    }

    public function getClassicalVideo(){ 
       $Resource = D('Resource');
       $classicalVideo = $Resource->getInformation();
       return $classicalVideo;
    }

    public function getSuccessPeople(){
      $PersonnelData = D('PersonnelData');
      $successPeople = $PersonnelData->getSuccessPeople();
      return $successPeople;
    }

   	public function getFieldExpert(){
      $PersonnelData = D('PersonnelData');
      $fieldExpert = $PersonnelData->getFieldExpert();
      return $fieldExpert;
    }

    public function getGoodTeach(){
      $PersonnelData = D('PersonnelData');
      $GoodTeach = $PersonnelData->getGoodTeach();
      return $GoodTeach;
    }

    public function getHotTopics(){
      
      $MediaData = D('MediaData');
      $hotTopics = $MediaData -> getNewsList(1); //group 为1 时 表示 热门资讯
      return $hotTopics;
    }   
}