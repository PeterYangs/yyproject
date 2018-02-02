<?php
namespace Auth\Start;
use think\Db;

/**
DROP TABLE IF EXISTS `think_auth_group`;
CREATE TABLE `think_auth_group` (
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
`title` char(100) NOT NULL DEFAULT '' COMMENT '用户组（角色）名',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
`rules` char(80) NOT NULL DEFAULT '' COMMENT '权限表id,用逗号分开',
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='权限用户（角色）组表';


-- ----------------------------
-- Table structure for `think_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `think_auth_group_access`;
CREATE TABLE `think_auth_group_access` (
`uid` mediumint(8) unsigned NOT NULL,
`group_id` mediumint(8) unsigned NOT NULL,
UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
KEY `uid` (`uid`),
KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户明显表（用户属于哪个用户组）';


-- ----------------------------
-- Table structure for `think_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `think_auth_rule`;
CREATE TABLE `think_auth_rule` (
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
`name` char(80) NOT NULL DEFAULT '',
`title` char(20) NOT NULL DEFAULT '',
`type` tinyint(1) NOT NULL DEFAULT '1',
`status` tinyint(1) NOT NULL DEFAULT '1',
`condition` char(100) NOT NULL DEFAULT '',
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='权限规则表';
 */



//新

/**
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for auth_group
                       -- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组（角色）名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态,暂未用到',
  `rules` varchar(1000) NOT NULL DEFAULT '' COMMENT '权限表id,用逗号分开',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Table structure for auth_group_access
                       -- ----------------------------
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access` (
`uid` mediumint(8) unsigned NOT NULL COMMENT '用户id，一般对应后台管理员id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组（角色）表id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组明细表';

-- ----------------------------
-- Table structure for auth_rule
                       -- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '' COMMENT '权限码名称，一般用 controller+action',
  `title` char(50) NOT NULL DEFAULT '' COMMENT '权限码描述',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，暂未用到',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '暂未用到',
  `group_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分组名称，权限码太多了，做一下显示分组，比如商品管理组',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='权限码表';
*/


class Auth {


    protected $auth_group='auth_group';//用户角色表(用户组表)

    protected $group_access="auth_group_access";//用户明细表(用户属于哪个组)

    protected $auth_rule="auth_rule";//权限码表


    protected $user='user';//用户表



    function check($rule,$uid){


        //转小写
        $rule=strtolower($rule);

        $list= $this->getRuleList($uid);



        if($list===true)  return true;

        if(!$list) return [];

        if(in_array($rule,$list)) return $list;

        return false;



    }


    protected function getRuleList($uid){


        $re=Db::table($this->group_access)
            ->alias('ga')
            ->join($this->auth_group." ag",'ga.group_id = ag.id','left')
            ->where('ga.uid',$uid)
            ->find()
        ;


        if(!$re) return [];


        //超级管理员
        if($re['group_id']===0) return true;



        $res=Db::table($this->auth_rule)->where('id','in',$re['rules'])->select();


        if(!$res) return [];


        //取这个数组列表name值，合并成新数组
        $res=array_column($res,'name');

        $arr=[];
        //全部转小写
        foreach ($res as $key=>$value){

            $v=strtolower($value);

            $arr[]=$v;
        }


        return $arr;


    }





}
