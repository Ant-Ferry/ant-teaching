<?php
namespace Home\Controller;
use OT\DataDictionary;

/**
 * 新闻控制器
 * 主要新闻页面
 */
class VideoController extends HomeController {
	//设置页面特征：标题，控制器名称，
	public $PageFeature = array('ControllerName' =>'Resources/index', 'Title' =>'教学资源','Title1' =>'视频');

	 //前置操作方法
    public function _before_index(){
        //在前置方法中 操作判断用户
		$this->assign('PageFeature',$this->PageFeature);
    }
	//系统首页
    public function index($id='-1'){
        //判读是否有参数
        
        if($id=='-1'){
            $this->error('页面不存在','/Article/error',1);
        }
        if($uid = is_login()){

            //获得基本信息
            $Details = $this->getDetails($id);
            $this->assign('Details',$Details);

            //获得视频详情
            $SetList = $this->getSetList($Details['set']);
            $this->assign('SetList',$SetList);
            $this->addBookmark($id);
            $this->addAnnal($uid,$id,$Details['title'],$Details['authority']);

            $this->display();

        } else {
            //$this->redirect('User/login')
            // 记录当前列表页的cookie
            Cookie('__forward__',$_SERVER['REQUEST_URI']);
            $this->error('登陆后才可观看视频',U('Home/User/login'),3);
        }
        
    }

    public function addBookmark($id){
        $Resource = D('Resource');
        $returnData = $Resource -> addBookmark($id);
    }

    public function addAnnal($uid,$vid,$title,$narrator){
        $Annal = D('PlayVideo');
        //$returnData = $Annal -> ;
        //先查询是否有记录
        if(!$Annal->where(array('vid' => $vid ))->select())
        {
            $map = array();
            $map['uid'] = $uid;
            $map['vid'] = $vid;
            $map['title'] = $title;
            $map['narrator'] = $narrator;
            $map['create_time'] = NOW_TIME;
            $map['status'] = 1;
            //没有记录再添加
            $returnData = $Annal->create($map,1);
            if(!$returnData)
               exit($this->getError());
            else
                $Annal->add($map);
           
        }
        
    }

   /**
    * [getDetails 获得详情]
    * @param  [type] $id [资源id]
    * @return [type]     [description]
    */
    public function getDetails($id){
        $Resource = D('Resource');
        $returnData = $Resource -> selectReturn($id);
        //var_dump($returnData);
        return $returnData[0];
    }

    /**
     * [getSetList 获得集合列表]
     * @param  [type] $set [description]
     * @return [type]      [description]
     */
    public function getSetList($set){
        $Resource = D('Resource');
        $returnData = $Resource -> selectReturn(-1,$set);
        //var_dump($returnData);
        return $returnData;
    }

    /**
     * [getMediaValue 获得资源值]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getMediaValue($id){
        $MediaData = D('MediaData');
        $returnData = $MediaData->getNewValue($id);
        //var_dump($returnData);
        return $returnData;
    }

    /**
     * [getMediaContent 获得资源内容]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getMediaContent($id){
        $MediaArticle = D('MediaArticle');
        $returnData = $MediaArticle->getArtic($id);
        //var_dump($returnData);
        //var_dump($returnData);
        if($returnData){
            $MediaArticle->addBookmark($returnData['id']);
        }
        return $returnData;
    }
}
