<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/14
 * Time: 14:21
 */

namespace addons\assistact\logic;

use think\Db;
use addons\assistact\model\PromotionAssist;
use think\Exception;

class assistLogic
{
    /**
     * 助力活动id
     * @var
     */
    protected static $assist_id;
    protected static $user_id;
    protected static $sid;

    /**
     * 设置助力活动id
     * @param $id
     * @return $this
     */
    public  function setAssistId($id)
    {
        self::$assist_id = $id;
        return $this;
    }

    /**
     * 设置用户id
     * @param $id
     * @return $this
     */
    public function  setUserId($id){
        self::$user_id  = $id;
        return $this;
    }

    /**
     * 活动发起id（assist_leader表id）
     * @param $id
     * @return $this
     */
    public function  setSid($id){
        self::$sid  = $id;
        return $this;
    }

    /**
     * 获取活动信息
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getInfo(){
        $model =  new PromotionAssist();
        $info = $model->with(['assist_reward'=>function($query){
            $query->field('assist_id,reward_level,reward_name,reward_img,reward_num')->order('reward_level asc');
        }])->where('id',self::$assist_id)->cache(true)->find();
        if(!$info){
            ajaxReturn(array('status'=>0,'msg'=>'没找到相关活动信息'));
        }
        return $info;
    }

    /**
     * 给好友助力
     */
    public function doSupply(){
        if(!self::$sid) {
            ajaxReturn(array('status'=>0,'msg'=>'找不到活动信息'));
        }
        $this->checkActStatus();
        $is_supply = $this->checkIsSupply();
        if($is_supply){
            ajaxReturn(array('status'=>0,'msg'=>'已经为好友助力过了'));
        }
        $num = mt_rand(0,20); //随机生成助力数
        $res2 =  Db::name('assist_supply')->save(['assist_id'=>self::$assist_id,'sid'=>self::$sid,'user_id'=>self::$user_id,'supply_num'=>$num]);
        Db::name('assist_leader')->where('id',self::$sid)->setInc('receive_num',$num);
        if(!$res2){
            ajaxReturn(array('status'=>0,'msg'=>'助力失败，请重试'));
        }
        ajaxReturn(array('status'=>1,'msg'=>'助力成功','result'=>$num));
    }

    /**
     * 发起助力
     */
    public function doPromAct (){
        $this->checkActStatus();
        $status = $this->checkIsNew();
        if($status>0){
            ajaxReturn(array('status'=>1,'msg'=>'已经发起过助力啦，快分享给好友助力吧','result'=>aurl('assistact://act/index',array('id'=>self::$assist_id,'sid'=>$status))));
        }
        //发起助力
        $id = Db::name('assist_leader')->insertGetId(['assist_id'=>self::$assist_id,'user_id'=>self::$user_id,]);
        if(!$id){
            ajaxReturn(array('status'=>0,'msg'=>'操作失败，请重试'));
        }
        ajaxReturn(array('status'=>1,'msg'=>'发起成功','result'=>aurl('assistact://act/index',array('id'=>self::$assist_id,'sid'=>$id))));
    }


    /**
     * 校验是否发起助力
     * @return int 0没发起过 res.id 发起对应id
     */
    public function checkIsNew(){
        $is_new = 0;
        $res = Db::name('assist_leader')->where(['assist_id'=>self::$assist_id,'user_id'=>self::$user_id])->find();
        if($res){
            $is_new = $res['id'];
        }
        return $is_new;
    }

    /**
     * 校验是否获奖，获奖则返回中奖信息
     * @return array|int
     */
    public function checkIsAward(){
        $res = Db::name('assist_award')->where(['assist_id'=>self::$assist_id,'user_id'=>self::$user_id])->find();
        if($res){
            $info = Db::name('assist_reward')->where(['id'=>$res['reward_id']])->field('reward_name,reward_img')->find();
            $data = [
                'id'=>$res['id'],
                'reward_name'=>$info['reward_name'],
                'reward_img'=>$info['reward_img']
            ];
            return $data;
        }
        return 0;

    }

    /**
     * 校验是否已经助力过
     * @return int 0可以助力 1已经助力 2 不需要助力
     */
    public function checkIsSupply(){
        if(self::$sid){
            $is_supply = 0;
            $res = Db::name('assist_supply')->where(['sid'=>self::$sid,'user_id'=>self::$user_id])->count();

            if($res>0){
                $is_supply = 1; //助力过了
            }
            return $is_supply;
        }
        return 1;   //TODO 不需要助力
    }

    /**
     * 助力活动结束
     */
    public function doAssistEnd(){

        Db::startTrans();
        try{
            //分配奖励
            //获取所有奖品
            $r = Db::name('assist_award')->where('assist_id',self::$assist_id)->lock(true)->select();
            if($r){
                return false;
            }
            Db::name('promotion_assist')->where('id',self::$assist_id)->update(['is_end'=>1]);
            $res = Db::name('assist_reward')->where('assist_id',self::$assist_id)->field('id,reward_level,reward_num')->order('reward_level asc')->select();
            $i = 0;
            $reward = array();

            foreach($res as $key=>$val){
                $reward[] = $val;
            }

            for ($level = 0;$level<$res[count($res)-1]['reward_level'];$level++){
                for ($num = 0;$num< $res[$level]['reward_num'];$num++){
                    $reward[$i] = $res[$level]['id'];
                    $i++;
                }
            }
            $res2 = $this->getRanking(false);
            foreach ($res2 as $key=>$value){
                unset($res2[$key]['receive_num']);
                $res2[$key]['reward_id'] = $reward[$key];
            }
            Db::name('assist_award')->insertAll($res2);
            Db::commit();
        }catch(Exception $e){
            Db::rollback();
        }


    }

    /**
     * 获取助力排行榜
     * @param bool $get 是否获取排行榜相关用户信息
     * @return false|\PDOStatement|string|\think\Collection|\think\Paginator
     */
    public function getRanking($get = true){
        if($get){
            $res = Db::name('assist_leader')->alias('a')->join('users u','a.user_id=u.user_id')
                ->where('a.assist_id',self::$assist_id)->field('u.head_pic,u.nickname,a.receive_num as num')->order('a.receive_num desc')->paginate(9)->each(function($item,$key){
                    $item['nickname'] =  substr($item['nickname'],0,1).'**';
                    return $item;
                });
        }else{
            $limit = Db::name('assist_reward')->where('assist_id',self::$assist_id)->sum('reward_num');
            $res = Db::name('assist_leader')->where('assist_id',self::$assist_id)->order('receive_num desc')->field('id',true)->limit($limit)->select();
        }

        if(!res){
            ajaxReturn(array('status'=>0,'msg'=>'无更多数据'));
        }

        return $res;

    }

    /**
     * 获取好友助力榜
     * @return \think\Paginator
     */
    public function getFriendRank(){
        if(!self::$sid){
            ajaxReturn(array('status'=>0,'msg'=>'未找到相关数据'));
        }
        $res = Db::name('assist_supply')->alias('a')->join('users u','a.user_id=u.user_id')
            ->where(['a.assist_id'=>self::$assist_id,'a.sid'=>self::$sid])->field('u.head_pic,u.nickname,a.supply_num as num')->order('a.supply_num desc')->paginate(9)->each(function($item,$key){
                $item['nickname'] =  substr($item['nickname'],0,1).'**';
                return $item;
            });
        if(!$res){
            ajaxReturn(array('status'=>0,'msg'=>'无更多数据'));
        }
        ajaxReturn(array('status'=>1,'msg'=>'获取成功','result'=>$res));
    }

    /**
     * 校验助力活动状态
     */
    private function checkActStatus(){
        $res = Db::name('promotion_assist')->where('id',self::$assist_id)->field('start_time,end_time')->find();
        if(!res || ($res['start_time'] > time())){
            ajaxReturn(array('status'=>0,'msg'=>'活动不存在或活动未开始'));
        }
        if($res['end_time'] < time()){
            ajaxReturn(array('status'=>0,'msg'=>'活动已结束'));
        }
    }



}