<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/15
 * Time: 17:28
 */
namespace addons\assistact\validate;
use think\Validate;
use think\Db;

class Assist extends Validate
{
    // 验证规则
    protected $rule = [
        ['assist_id','number|checkAssistId','活动未找到|活动当前状态不可修改'],
        ['title','require|max:16','活动标题必须|活动标题最多10个字符'],
        ['done_reward', 'checkDoneReward'],  //TODO 校验助力可得奖品
        ['reward', 'require|checkReward','请设置奖品|奖品设置错误请修改'],
        ['start_time','require|checkEndTime','请填开始时间|结束时间不得小于或等于开始时间'],
        ['description','require','请填写活动规则']
    ];

    protected $sence=[
        'add'  => ['title','reward','start_time','description','done_reward'],
        'edit' => ['assist_id','title','reward','start_time','description','done_reward'],
    ];
    /**
     * 检查结束时间
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkEndTime($value, $rule ,$data)
    {
        if($value < time()){
            return '开始时间不得小于当前时间';
        }
        return ($value > $data['end_time'] ) ? false : true;
    }

    /**
     * 检查奖品设置
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkReward($value, $rule ,$data){
        if(!is_array($value)){
            return '请设置奖品';
        }
        foreach ($value as $v){
            if(empty($v['reward_name']) || empty($v['reward_img']) || empty($v['reward_num'])){
                return '请按要求填写奖品信息';
            }
            if(!is_numeric($v['reward_num'])){
                return '请填写奖品数量';
            }
            if($v['reward_num']>99){
                return '单种奖品数量不得大于99个';
            }
            if(!is_file(ROOT_PATH.$v['reward_img'])){
                return '请先上传奖品图片';
            }
        }
        return true;
    }




    /**
     * 该活动是否可以编辑
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkAssistId($value, $rule ,$data)
    {
        $res = Db::name('promotion_assist')->where('id',$value)->value('start_time');
        if(!$res || $res < time()){
            return '助力活动已经开始，不允许修改';
        }
        return true;
    }

    /**
     * 校验可得奖品
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    public function checkDoneReward($value,$rule,$data){
        return true;
    }
}