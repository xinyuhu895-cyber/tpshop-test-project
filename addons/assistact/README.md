## 爱心助力开发文档
1. 目录结构
    ```            
    ├─addons                插件目录
    │  ├─assistact          爱心助力主目录
    │  │  ├─controller      控制器目录
    │  │  │  ├─Act.php      操作控制器       
    │  │  │  ├─Admin.php    管理控制器
    │  │  │  └─Base.php     基础控制器
    │  │  ├─view            视图目录
    │  │  ├─model           模型目录
    │  │  ├─Assistact.php   插件基类
    │  │  ├─install.lock    安装标识（存在即已安装）
    │  │  └─config.php      插件配置  
    ```
2. 命令行安装模式
    
    ```
     php think assistact:build             //创建不执行安装
     php think assistact:build --install   //创建并安装
     php think assistact:build --uninstall //卸载插件
    ```
3. 公共函数
>  构造url（aurl）
    
    ```
    aurl('assistact://act/index')  构造链接：addons/exec/assistact-act-index
    //    assistact://act/index    模块名://控制器名/操作名
    ```
4. 钩子方法
```
在Assistact基类中定义，如：(规范命名方式以Hook结尾)
    public function adminHook(){
        return false;
    }
    
    在php中直接可调用hook('adminHook')，html标签中使用{:hook(;adminHook;)}
    
    钩子配置 application\extra\addons
    return [
        'autoload'=>true,  //自动加载钩子（debug模式）
            'hooks'=>[     //当autoload为false时，加载hooks中定义的钩子
                'adminHook'=>'assistact'  //key为钩子函数，value为所属的插件，多插件可用数组
            ]
    ];
```

5. 数据库
> 表: assist_award  获奖信息

| Field | Type| Comment | 
| -------- | -------- | -------- |
| id | int(11) unsigned NOT NULL|  主键id| 
|assist_id|int(11) NOT NULL|助力活动id|
|user_id|int(11) NOT NULL|获奖用户id|
|reward_id|int(11) NOT NULL|获奖奖品id|
|user_name|varchar(10) NULL|获奖用户姓名|
|user_address|varchar(32) NULL|获奖用户地址|
|user_phone|varchar(11) NULL|获奖用户联系电话|

> 表：assist_leader  活动发起列表

|Field|Type|Comment|
| -------- | -------- | -------- |
|id|int(11) unsigned NOT NULL|主键id|
|assist_id|int(11) NOT NULL|助力活动id|
|user_id|int(11) NOT NULL|发起用户id|
|receive_num|int(11) NOT NULL|收到助力总数|

> 表：assist_supply 好友助力列表

|Field|Type|Comment|
| -------- | -------- | -------- |
|id|int(11) unsigned NOT NULL|主键id|
|assist_id|int(11) NULL|助力活动id|
|sid|int(11) NOT NULL|好友发起助力事件id|
|user_id|int(11) NOT NULL|提供助力用户id|
|supply_num|int(2) NOT NULL|提供助力数|

> 表： promotion_assist 助力活动列表

|Field|Type|Comment|
| -------- | -------- | -------- |
|id|int(11) unsigned NOT NULL|
|title|varchar(10) NOT NULL|助力活动标题|
|start_time|int(11) NOT NULL|活动开始时间|
|end_time|int(11) NOT NULL|活动结束时间|
|done_reward|varchar(20) NULL|用户助力获得奖励|
|description|text NULL|活动描述|
|is_end|int(1) NOT NULL|活动是否结束|








    
