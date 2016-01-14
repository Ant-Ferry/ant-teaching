<?php
namespace Home\Controller;
use OT\DataDictionary;

/**
 * 新闻控制器
 * 主要新闻页面
 */
class ShowController extends HomeController {
	//设置页面特征：标题，控制器名称，
	public $PageFeature = array('ControllerName' =>'News/index', 'Title' =>'图文');

	 //前置操作方法
    public function _before_index(){
        //在前置方法中 操作判断用户
		$this->assign('PageFeature',$this->PageFeature);
    }
	//系统首页
    public function index($type='-1',$id='-1'){
        //判读是否有参数
        if($type =='-1'||$id=='-1'){
            $this->error('页面不存在','/Article/error',1);
        }

        switch ($type) {
            case '3':
                //获得id参数
                $MediaValue = $this->getMediaValue($id);
                $this->assign('mediaValue',$MediaValue);

                //获得此资源类型，分组 和 此类资源列表
                $groupList = $this->getGroupList($id);
                $this->assign('groupList',$groupList);

                //获得视图
                $MediaContent = $this->getMediaContent($id);
                $this->assign('mediaContent',$MediaContent);

                break;
            default:
                # code...
                break;
        }

        $this->display();
    }

    /**
     * [getCollegeNews 获得学院新闻]
     * @return [type] [description]
     */
    public function getCollegeNews(){
        $MediaData = D('MediaData');
        $collegeNews = $MediaData -> getNewsPage(2,'1,3'); //group 为2 时 表示 学院新闻 
        return $collegeNews;
    }

    /**
     * [getNotice 获得通知]
     * @return [type] [description]
     */
    public function getNotice(){
        $MediaData = D('MediaData');
        $notice = $MediaData -> getNewsPage(3,'1,4'); //group 为3 时 表示通知
        return $notice;
    }

    /**
     * [getMediaValue 获得资源值]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getMediaValue($id){
        $MediaData = D('MediaData');
        $returnData = $MediaData->getNewValue($id);
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
        
        if($returnData){
            $MediaArticle->addBookmark($returnData['id']);
        }
        return $returnData;
    }

    public function getGroupList($id){
        $MediaData = D('MediaData');

        $group = $MediaData->where('id='.$id)->field('group,type')->find();
        
        $map['type'] = $group['type'];
        $map['group'] = $group['group'];

        $groupList = $MediaData->where($map)->field('id,title')->select();

        //$arrayReturn = array('type' => $group['type'],'group' => $group['group'],'groupList' => $groupList);
        return $groupList;
       
    }
}
