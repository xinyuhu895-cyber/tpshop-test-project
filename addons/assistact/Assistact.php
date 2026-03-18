<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/11/17
 * Time: 15:32
 */

namespace addons\assistAct;
use app\common\util\TpshopException;
use think\Addons;
use think\Db;


class AssistAct extends Addons
{
    /**
     * 工具信息描述
     * @var array
     */
    protected $info = [
        'name' => 'assistAct',
        'title' => '微助力',
        'description' => '营销工具-微助力',
        'status' => false,  //预留状态，暂无实际含义
        'author' => 'L1PY',
        'version' => '1.0'
    ];

    /**
     * 创建插件
     */
    public function build(){
        //TODO 添加插件列表记录等操作
    }

    /**
     * 安装插件
     */
    public  function install()
    {
        Db::startTrans();
        try{
            $sql = init_sql($this->addons_path.'install.sql');  //初始化sql
            foreach ($sql as $v){
                $r = Db::execute($v);
                if(!is_numeric($r)){
                    throw new TpshopException('安装错误',0,['status'=>0,'msg'=>'安装出现异常','result'=>$r]);
                }
            }
            Db::commit();
        }catch (TpshopException $e){
            Db::rollback();
            return $e->getErrorArr();
        }
        touch($this->addons_path.'install.lock');
        return ['status'=>1,'msg'=>'安装成功','result'=>$r];
    }

    /**
     * 卸载插件
     */
    public  function uninstall()
    {
        Db::startTrans();
        try{
            $sql = init_sql($this->addons_path.'uninstall.sql');  //初始化sql
            foreach ($sql as $v){
                $r = Db::execute($v);
                if(!is_numeric($r)){
                    throw new TpshopException('ERROR',0,['status'=>0,'msg'=>'卸载时出现异常','result'=>$r]);
                }
            }
            Db::commit();
        }catch (TpshopException $e){
            Db::rollback();
            return $e->getErrorArr();
        }
        file_exists($this->addons_path.'install.lock') && unlink($this->addons_path.'install.lock');
        return ['status'=>1,'msg'=>'卸载成功','result'=>$r];
    }





    public function assistInstallHook()
    {
        ajaxReturn($this->install());
    }

    public function assistUninstallHook()
    {
        ajaxReturn($this->uninstall());
    }



}