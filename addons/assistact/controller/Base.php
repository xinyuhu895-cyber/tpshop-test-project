<?php
/**
 * Created by PhpStorm.
 * User: L1PY
 * Date: 2018/12/14
 * Time: 10:52
 */

namespace addons\assistact\controller;

use think\addons\Controller;
use think\Db;
use think\Session;


class Base extends Controller
{
    /**
     * @var 当前用户
     */
    protected static $user;
    /**
     * @var 管理员id
     */
    protected static $admin_id = 0;

    public function __construct()
    {
        parent::__construct();
        //验证用户
        ('admin' == $this->controller) ? $this->checkAdmin(): $this->checkUser();

    }

    /**
     * 校验后台登录
     */
    private function checkAdmin(){
        Session::start();
        header("Cache-control: private");
        if (session('admin_id') > 0) {
            self::$admin_id = session('admin_id');
        }else {
            session('from_url',$this->request->url());
            $this->error('请先登录', U('Admin/Admin/login'), null, 1);
        }
    }

    /**
     * 校验用户登录情况
     * @return array
     */
    private function checkUser(){
        $user = $this->checkSession();
        if(!$user){
            $this->error('请先登录', U('mobile/user/login'), null, 1);
        }
        //TODO api校验
//        $token = I('token/s',false);
//        if(!$token || empty($token)){
//            abort(404,'请先登录账户');
//        }
//
//        $user = Db::name('users')->where('token', $token)->field('id,last_login')->find();
//        if (empty($user)) {
//            return ['status'=>-101, 'msg'=>'登录超时,请重新登录!'];
//        }
//
//        if(time() - $user['last_login'] > C('APP_TOKEN_TIME')) {  //3600
//            return ['status'=>-102, 'msg'=>'登录超时,请重新登录!', 'result'=>(time() - $user['last_login'])];
//        }
        self::$user = $user;
        //$this->checkCondition();
    }

    /**
     * 校验登录
     * @return bool
     */
    private function checkSession(){
        $user_temp = session('user');
        if (isset($user_temp['user_id']) && $user_temp['user_id']) {
            $user = M('users')->where("user_id", $user_temp['user_id'])->find();
            return $user;
        }
        session('user', null);
        return false;
    }

    /**
     *校验用户参与活动是否符合条件
     */
    private function checkCondition(){
        $res = Db::name('oauth_users')->where(['user_id'=>self::$user['user_id']])->count();
        if(!$res){
            ajaxReturn(['status'=>0, 'msg'=>'由于活动限制，您不符合活动参与条件']);
        }
    }
}