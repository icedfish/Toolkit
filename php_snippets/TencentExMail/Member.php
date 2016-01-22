<?php

namespace TencentExMail;

class Member
{
    //邮箱地址
    public $email;
    //姓名
    public $name;
    //性别,0=不设置,1=男,2=女
    public $gender = 0;
    //职位
    public $position;
    //联系电话(作为str处理)
    public $tel;
    //手机(作为str处理)
    public $mobile;
    //编号
    public $ext_id;
    //密码
    public $password;
    //账号状态,0=不设置状态,1=启用帐号,2=禁用帐号
    public $open_type = 0;
    //企业邮箱中的部门
    public $team;
}
