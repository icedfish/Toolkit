<?php

namespace TencentExMail;

use Exception;

class Util
{
    //请求超时时间
    const REQUEST_TIMEOUT = 4;

    //用户状态表示
    const MEMBER_DISABLE = 2;
    const MEMBER_ENABLE = 1;

    //用户对应操作
    const MEMBER_ACTION_ADD = 2;
    const MEMBER_ACTION_SYNC = 3;

    //返回状态值
    const RETURN_ERROR = 'error';
    const RETURN_SUCCESS = 'success';

    private $access_token;

    //腾讯企业邮箱的id和key
    private $client_id = '';
    private $client_secret = '';

    //邮箱后缀
    public $email_suffix = '';


    function __construct()
    {
        //从配置文件初始化
        $this->client_id = config('services.exmail.client_id');
        $this->client_secret = config('services.exmail.client_secret');
        $this->email_suffix = config('services.exmail.email_suffix');

        if (trim($this->client_id) == '' || trim($this->client_secret) == '' || trim($this->email_suffix) == '') {
            throw new Exception('Whoops! Your config is something wrong...');
        }
    }


    /**
     * 集成登陆，返回跳转用的url
     *
     * @param $email
     * @return array
     */
    public function getLoginUrl($email)
    {
        try {
            $key = $this->getAuthKey($email);
            $redirect_params = [
                'fun' => 'bizopenssologin',
                'method' => 'bizauth',
                'agent' => $this->client_id,
                'user' => $email,
                'ticket' => $key,
                'access_token' => $this->getToken(),
            ];

            return [
                'code' => self::RETURN_SUCCESS,
                'url' => 'https://exmail.qq.com/cgi-bin/login?' . http_build_query($redirect_params),
            ];

        } catch (Exception $e) {
            return [
                'code' => self::RETURN_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * 得到登陆/获取邮件需要的auth_key
     *
     * @param $email
     * @return mixed
     * @throws Exception
     */
    private function getAuthKey($email)
    {
        $request_array = ['Alias' => $email, 'access_token' => $this->getToken()];
        $response = $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/mail/authkey', $request_array);
        return $response['auth_key'];
    }

    /**
     * @return mixed
     */
    private function getToken()
    {
        if ($this->access_token == null) {
            $this->access_token = self::oauth();
        }

        return $this->access_token;
    }

    /**
     *
     * 用client_id和client_secret认证，取得access_token
     *
     * @return mixed
     * @throws Exception
     */
    private function oauth()
    {

        $request_array = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        ];

        $response = $this->postUrl('https://exmail.qq.com/cgi-bin/token', $request_array);

        return $response['access_token'];
    }

    /**
     *
     * 以post方式请求一个url，返回jsonDecode的数组
     *
     * @param $url
     * @param $post_data
     * @return mixed
     * @throws Exception
     */
    private function postUrl($url, array $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_TIMEOUT, self::REQUEST_TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::REQUEST_TIMEOUT);

        $output = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);

        curl_close($ch);

        if ($curl_errno > 0) {
            //curl错误处理
            throw new Exception($curl_error, $curl_errno);
        } else {
            if ($http_code != 200) {
                //http错误处理
                throw new Exception('Whoops! Connection Failed...');
            }
            $json_content = json_decode($output, true);

            if (isset($json_content['errcode'])) {
                //腾讯返回的错误处理
                throw new Exception($json_content['error'], $json_content['errcode']);
            }
            return $json_content;
        }
    }

    /**
     *
     * 得到所有用户，返回包含每个人邮箱的array
     *
     * @return array
     */
    public function getAllMembers()
    {
        try {
            $request_array = ['PartyPath' => null, 'access_token' => $this->getToken()];
            $response = $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/partyuser/list', $request_array);

            return [
                'code' => self::RETURN_SUCCESS,
                'content' => $response['List'],
            ];
        } catch (Exception $e) {
            return [
                'code' => self::RETURN_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }


    /**
     *
     * 操作Member，不存在则新增，存在则刷新
     * ->如果邮箱非法返回Failed
     *
     * @param Member $member
     * @return array
     */
    public function syncMember(Member $member)
    {
        try {
            $request_array = ['email' => $member->email, 'access_token' => $this->getToken()];
            $response = $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/user/check', $request_array);
            $flag = $response['List'][0]['Type'];

            if ($flag == -1) {
                //邮箱名非法
                return [
                    'code' => self::RETURN_ERROR,
                    'msg' => 'Your email:(' . $member->email . ') is illegal..'
                ];
            } else {
                $request_array = [
                    'Alias' => $member->email,
                    'Name' => $member->name,
                    'Gender' => $member->gender,
                    'Position' => $member->position,
                    'Tel' => $member->tel,
                    'Mobile' => $member->mobile,
                    'ExtId' => $member->ext_id,
                    'OpenType' => $member->open_type,
                    'Password' => md5($member->password),
                    'Md5' => 1,
                    'PartyPath' => $member->team,
                    'access_token' => $this->getToken(),
                ];

                //判断添加还是刷新
                if ($flag == 0) {
                    $request_array['Action'] = self::MEMBER_ACTION_ADD;
                } else {
                    $request_array['Action'] = self::MEMBER_ACTION_SYNC;
                }

                $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/user/sync', $request_array);

                return [
                    'code' => self::RETURN_SUCCESS,
                ];
            }
        } catch (Exception $e) {
            return [
                'code' => self::RETURN_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }


    /**
     *
     * 禁用用户
     *
     * @param $email
     * @return array
     */
    public function disableMember($email)
    {
        try {
            $request_array = ['Alias' => $email, 'access_token' => $this->getToken()];
            $response = $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/user/get', $request_array);

            $response['OpenType'] = self::MEMBER_DISABLE;
            $response['Action'] = self::MEMBER_ACTION_SYNC;
            $response['access_token'] = $this->getToken();

            unset($response['PartyList']);//去掉其中的array

            $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/user/sync', $response);

            return [
                'code' => self::RETURN_SUCCESS,
            ];

        } catch (Exception $e) {
            return [
                'code' => self::RETURN_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }


    /**
     *
     * 启用用户
     *
     * @param $email
     * @return array
     */
    public function enableMember($email)
    {
        try {
            $request_array = ['Alias' => $email, 'access_token' => $this->getToken()];
            $response = $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/user/get', $request_array);

            $response['OpenType'] = self::MEMBER_ENABLE;
            $response['Action'] = self::MEMBER_ACTION_SYNC;
            $response['access_token'] = $this->getToken();

            unset($response['PartyList']);//去掉其中的array

            $this->postUrl('http://openapi.exmail.qq.com:12211/openapi/user/sync', $response);

            return [
                'code' => self::RETURN_SUCCESS,
            ];

        } catch (Exception $e) {
            return [
                'code' => self::RETURN_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}