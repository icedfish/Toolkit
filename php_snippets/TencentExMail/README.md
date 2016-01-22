##疼讯的企业邮箱接口调用


###=============添加/刷新用户===============

    $member = new Member();
    $member->name = '蓝白';
    $member->email = 'whiteblue@woniu.life';
    $member->password = '123456';

    $util = new Util();
    $result = $util->syncMember($member);

    if ($result['code'] == 'success') {
        dd('添加成功');
    } else {
        dd('添加失败' . $result['msg']);
    }

###=============禁用/启用用户===============

    $util = new Util();
    $result = $util->disableMember('whiteblue@woniu.life');
    if ($result['code'] == 'success') {
        dd('操作成功');
    } else {
        dd('操作失败' . $result['msg']);
    }

###=============集成登陆====================

    $util = new Util();
    $result = $util->getLoginUrl('whiteblue@woniu.life');
    if ($result['code'] == 'success')) {
        return redirect($result['url']);
    } else {
        dd('操作失败' . $result['msg']);
    }


###=============所有用户====================

    $util = new Util();
    $result = $util->getAllMembers();
    if ($result['code'] == 'success') {
        dd($result['content']);
    } else {
        dd('操作失败' . $result['msg']);
    }

###============返回信息说明==================
    
    返回code为'success'则成功，返回code为'error'则失败，检查msg字段可得到信息。

                                                                                
                                                                                    --keyinan@wutongwan.org

