<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/14
 * Time: 10:51
 */

namespace addons\assistact\controller;


use addons\assistact\logic\assistLogic;


class Act extends Base
{

    protected static $assist_id;
    protected static $sid;   //助力活动发起id

    public function __construct()
    {
        parent::__construct();
        self::$assist_id = I('id/d',0);
        self::$sid = I('sid/d',0);
    }

    /**
     * 模板1入口
     */
    public function index(){
        $this->assign('id',self::$assist_id);
        $this->assign('sid',self::$sid );
        return $this->fetch();
    }

    /**
     * 模板2入口
     */
    public function index2(){
        $this->assign('id',self::$assist_id);
        $this->assign('sid',self::$sid );
        return $this->fetch();
    }

    /**
     * 获取活动信息
     */
    public function info(){
        if(!self::$assist_id){
            ajaxReturn(array('status'=>0,'msg'=>'不存在相关活动信息'));
        }
        $logic = new assistLogic();
        $logic->setAssistId(self::$assist_id)->setUserId(self::$user['user_id'])->setSid(self::$sid);
        $info = $logic->getInfo();
        if((0 == $info['is_end']) && (time() > $info['end_time'])){
            $logic->doAssistEnd();
            $info['is_end'] = 1;
        }
        $info['is_supply'] = $logic->checkIsSupply();
        $info['is_new'] = $logic->checkIsNew();
        $info['is_award'] = $logic->checkIsAward();
        $info['description'] = html_entity_decode($info['description']);
        ajaxReturn(array('status'=>1,'msg'=>'获取成功','result'=>$info));
    }

    /**
     * 助力总排行榜
     */
    public function ranking(){
        $logic = new assistLogic();
        $res = $logic->setAssistId(self::$assist_id)->getRanking();
        ajaxReturn(array('status'=>1,'msg'=>'获取成功','result'=>$res));
    }

    /**
     * 好友助力排行
     */
    public function friendRank(){
        $logic = new assistLogic();
        $logic->setAssistId(self::$assist_id)->setSid(self::$sid)->getFriendRank();
    }

    /**
     * 结束助力活动
     */
    public function assistEnd(){
        $logic = new assistLogic();
        $logic->setAssistId(self::$assist_id)->doAssistEnd();
    }

    /**
     * 给好友助力
     */
    public function supply(){
        $logic = new assistLogic();
        $logic->setAssistId(self::$assist_id)->setUserId(self::$user['user_id'])->setSid(self::$sid)->doSupply();
    }

    /**
     * 发起助力活动
     */
    public function promAct(){
        $logic = new assistLogic();
        $logic->setAssistId(self::$assist_id)->setUserId(self::$user['user_id'])->doPromAct();
    }
}