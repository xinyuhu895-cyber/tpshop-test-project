<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/17
 * Time: 15:17
 */

namespace addons\assistact\logic;

use addons\assistact\model\PromotionAssist;
use addons\assistact\validate\Assist;
use think\Db;


class adminLogic
{
    protected static $assist_id;
    protected static $is_edit = false;
    protected static $status;

    public function setAssistId($id){
        self::$assist_id = $id;
        self::$is_edit = true;
        return $this;
    }

    public function setStatus($status){
        self::$status = $status;
        return $this;
    }

    /**
     * 获取助力活动列表
     * @return \think\Paginator
     */
    public function getAssistList(){
        $model = new PromotionAssist();
        $info = $model->with(['assist_reward'=>function($query){
            $query->where('reward_level',1)->field('assist_id,reward_name');
        }])->field('id,title,start_time,end_time,is_end')->order('id desc')->paginate(10);
        return $info;
    }

    /**
     * 增加或修改活动
     */
    public function doAlter(){
        self::$is_edit ? $this->edit() : $this->add() ;
    }

    public function doChangeStatus(){
        $end_time = Db::name('promotion_assist')->where('id',self::$assist_id)->value('end_time');
        if(!$end_time || $end_time < time()){
            ajaxReturn(array('status'=>0,'msg'=>'活动已结束无法修改状态'));
        }
       $res =  Db::name('promotion_assist')->where('id',self::$assist_id)->update(['is_end'=>self::$status]);
        if(!$res){
            ajaxReturn(array('status'=>0,'msg'=>'状态修改失败，请重试'));
        }
        ajaxReturn(array('status'=>1,'msg'=>'状态修改成功'));
    }

    /**
     * 删除活动信息
     */
    public function doDelete(){
        $res = Db::name('assist_leader')->where('assist_id',self::$assist_id)->limit(1)->find();
        if($res){
            ajaxReturn(array('status'=>0,'msg'=>'检测有参与活动数据，不能删除'));
        }
        $res2 = Db::name('promotion_assist')->where('id',self::$assist_id)->delete();
        if(!$res2){
            ajaxReturn(array('status'=>1,'msg'=>'删除失败，请重试'));
        }
        Db::name('assist_reward')->where('id',self::$assist_id)->delete();
        ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
    }

    /**
     * 获取内容
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getDetail(){
//        $info = PromotionAssist::get(self::$assist_id);
//        return $info;

        $model = new PromotionAssist();
        $info = $model->with(['assist_reward'=>function($query){
            $query->order('reward_level asc');
        }])->where('id',self::$assist_id)->find();
        return $info;
    }

    /**
     * 添加
     */
    private function add(){
        $data = I('post.');
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $validate = new Assist();
        if(!$validate->scene('add')->check($data)){
            ajaxReturn(array('status'=>0,'msg'=>$validate->getError()));
        }
        $info = [
            'title'=>$data['title'],
            'start_time'=>$data['start_time'],
            'end_time'=>$data['end_time'],
            'done_reward'=>$data['done_reward'],
            'description'=>$data['description']
        ];
        $model = new PromotionAssist();
        $model->save($info);
        if(!$model->id){
            ajaxReturn(array('status'=>1,'msg'=>'添加失败'));
        }

        foreach ($data['reward'] as $key=> $val) {
            $data['reward'][$key]['reward_level'] = $key;
            $data['reward'][$key]['assist_id'] = $model->id;
        }
        Db::name('assist_reward')->insertAll($data['reward']);
        ajaxReturn(array('status'=>1,'msg'=>'添加成功'));
    }

    /**
     * 修改
     */
    private function edit(){
        $data = I('post.');
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $validate = new Assist();
        if(!$validate->scene('edit')->check($data)){
            ajaxReturn(array('status'=>0,'msg'=>$validate->getError()));
        }
        $info = [
            'id'=>self::$assist_id,
            'title'=>$data['title'],
            'start_time'=>$data['start_time'],
            'end_time'=>$data['end_time'],
            'done_reward'=>$data['done_reward'],
            'description'=>htmlentities($data['description'])
        ];
        Db::name('promotion_assist')->update($info);

        $res =  Db::name('assist_reward')->where('assist_id',self::$assist_id)->delete();
        if(!$res){
            ajaxReturn(array('status'=>0,'msg'=>'修改失败，请重试'));
        }
        foreach ($data['reward'] as $key=> $val) {
            $data['reward'][$key]['reward_level'] = $key;
            $data['reward'][$key]['assist_id'] = self::$assist_id;
        }
        Db::name('assist_reward')->insertAll($data['reward']);
        ajaxReturn(array('status'=>1,'msg'=>'修改成功'));
    }
}