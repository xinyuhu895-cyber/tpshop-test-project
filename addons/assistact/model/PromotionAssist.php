<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/17
 * Time: 17:06
 */

namespace addons\assistact\model;
use think\Model;

class PromotionAssist extends Model
{
    public function assistReward()
    {
        return $this->hasMany('assistReward','assist_id','id');
    }

    public function getDescriptionAttr($value){
        return html_entity_decode($value);
    }

    public function setDescriptionAttr($value){
        return htmlentities($value);
    }
}