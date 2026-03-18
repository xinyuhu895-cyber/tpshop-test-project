<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/15
 * Time: 14:15
 */

namespace addons\assistact\controller;


use addons\assistact\logic\adminLogic;

class Admin extends Base
{
    /**
     * 逻辑类实例
     * @var adminLogic
     */
    protected static $logic;

    public function __construct()
    {
        parent::__construct();
        self::$logic = new adminLogic();
    }

    /**
     * 爱心助力管理首页
     * @return mixed
     */
    public function index(){
        $list =  self::$logic->getAssistList();
        $this->assign('list',$list);
        $this->assign('page',$list->render());
        return $this->fetch();
    }

    /**
     * 爱心助力管理详情
     * @return mixed
     */
    public function detail(){
        $assist_id = I('assist_id/d');
        if($assist_id>0){
           $info =  self::$logic->setAssistId($assist_id)->getDetail();
            $this->assign('info',$info);
        }
        return $this->fetch();
    }

    /**
     * 修改活动信息
     * @return mixed
     */
    public function alter()
    {
        $assist_id = I('assist_id/d');
        if($assist_id>0){
            self::$logic->setAssistId($assist_id);
        }
        self::$logic->doAlter();
    }

    /**
     * 修改活动状态
     * @return mixed
     */
    public function status(){
        $assist_id = I('assist_id/d');
        $status = I('status/d',0);
        self::$logic->setAssistId($assist_id)->setStatus($status)->doChangeStatus();
    }

    /**
     * 删除活动
     */
    public function delete(){
        $assist_id = I('assist_id/d');
        if($assist_id>0){
            self::$logic->setAssistId($assist_id)->doDelete();
        }
    }


}