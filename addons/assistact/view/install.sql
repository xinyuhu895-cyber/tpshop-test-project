

DROP TABLE IF EXISTS `__PREFIX__assist_award`;

CREATE TABLE `__PREFIX__assist_award` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assist_id` int(11) NOT NULL COMMENT '助力活动id',
  `user_id` int(11) NOT NULL COMMENT '获奖用户id',
  `reward_id` int(11) NOT NULL COMMENT '获奖奖品id',
  `user_name` varchar(10) DEFAULT NULL COMMENT '获奖用户姓名',
  `user_address` varchar(32) DEFAULT NULL COMMENT '获奖用户地址',
  `user_phone` varchar(11) DEFAULT NULL COMMENT '获奖用户联系电话',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='助力获奖表';



DROP TABLE IF EXISTS `__PREFIX__assist_leader`;

CREATE TABLE `__PREFIX__assist_leader` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assist_id` int(11) NOT NULL COMMENT '助力活动id',
  `user_id` int(11) NOT NULL COMMENT '发起用户id',
  `receive_num` int(11) NOT NULL DEFAULT '0' COMMENT '收到助力总数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='助力互动发起表';



DROP TABLE IF EXISTS `__PREFIX__assist_reward`;

CREATE TABLE `__PREFIX__assist_reward` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assist_id` int(11) NOT NULL COMMENT '助力活动id',
  `reward_level` int(2) NOT NULL COMMENT '奖品等级',
  `reward_name` varchar(10) NOT NULL COMMENT '奖品等级名称',
  `reward_img` varchar(128) NOT NULL COMMENT '奖品图片',
  `reward_num` int(2) NOT NULL DEFAULT '1' COMMENT '奖品数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='助力活动奖品表';


DROP TABLE IF EXISTS `__PREFIX__assist_supply`;

CREATE TABLE `__PREFIX__assist_supply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assist_id` int(11) DEFAULT NULL COMMENT '助力活动id',
  `sid` int(11) NOT NULL COMMENT '好友发起助力事件id',
  `user_id` int(11) NOT NULL COMMENT '提供助力用户id',
  `supply_num` int(2) NOT NULL COMMENT '提供助力数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COMMENT='参与助力用户表';



DROP TABLE IF EXISTS `__PREFIX__promotion_assist`;

CREATE TABLE `__PREFIX__promotion_assist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(10) NOT NULL COMMENT '助力活动标题',
  `start_time` int(11) NOT NULL COMMENT '活动开始时间',
  `end_time` int(11) NOT NULL COMMENT '活动结束时间',
  `done_reward` varchar(20) DEFAULT NULL COMMENT '用户助力获得奖励',
  `description` text COMMENT '活动描述',
  `is_end` int(1) NOT NULL DEFAULT '0' COMMENT '活动是否结束',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='助力活动列表';


