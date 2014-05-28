<?php
/**
 * Created by Green Studio.
 * File: LoginController.class.php
 * User: TianShuo
 * Date: 14-1-25
 * Time: 上午10:39
 */

namespace Admin\Controller;

use Common\Controller\BaseController;
use Org\Util\Rbac;

/**
 * Class LoginController
 * @package Admin\Controller
 */
class LoginController extends BaseController
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->customConfig();

    }

    public function vertify()
    {

        $config = array(
            'fontSize' => 20,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => true,
        );


        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    /**
     *
     */
    public function _before_index()
    {
        $user_session = cookie('user_session');
        if (!empty($user_session)) {
            $authInfo = D('User', 'Logic')->where(array('user_session' => $user_session))->find();

            if (!empty($authInfo)) {
                $_SESSION[C('USER_AUTH_KEY')] = $authInfo['user_id'];

                if ($authInfo ['user_login'] == get_opinion('ADMIN')) {
                    $_SESSION [C('ADMIN_AUTH_KEY')] = true;
                }

                $log['log_user_id'] = $authInfo['user_id'];
                $log['log_user_name'] = $authInfo['user_login'];
                $log['log_password'] = $authInfo['user_pass'];
                $log['log_ip'] = get_client_ip();
                $log['log_status'] = 2;

                D('login_log')->data($log)->add();

                $this->redirect('Admin/Index/index');
            }


        }
    }

    /**
     *
     */
    public function index()
    {


        $this->display();
    }

    /**
     *
     */
    public function login()
    {
        // $ipLocation = new IpLocation();
        // $ip_info = $ipLocation->getIpInfo();

        $verify = new \Think\Verify();

        if (!$verify->check(I('post.vertify'))) {
            $this->error("验证码错误");
        }


        $map = array();
        $map['user_login'] = I('post.username');
        $map['user_status'] = array('gt', 0);

        $authInfo = RBAC::authenticate($map);
        if (false === $authInfo || $authInfo == null) {
            $log['log_user_id'] = -1;
            $log['log_user_name'] = I('post.username');
            $log['log_password'] = I('post.password');
            $log['log_ip'] = get_client_ip();
            $log['log_status'] = -1;

            D('login_log')->data($log)->add();

            $this->error('帐号不存在或已禁用！');
        } else {
            if ($authInfo['user_pass'] != encrypt(I('post.password'))) {
                $log['log_user_id'] = $authInfo['user_id'];
                $log['log_user_name'] = I('post.username');
                $log['log_password'] = I('post.password');
                $log['log_ip'] = get_client_ip();
                $log['log_status'] = 0;

                D('login_log')->data($log)->add();

                $this->error('密码错误或者帐号已禁用');
            }
            $_SESSION[C('USER_AUTH_KEY')] = $authInfo['user_id'];
            if ($authInfo['user_login'] == get_opinion('Admin')) {
                $_SESSION[C('ADMIN_AUTH_KEY')] = true;
            }

            //记住我
            if (I('post.remember') == 1) {
                if ($authInfo['user_session'] != '') {
                    cookie('user_session', $authInfo['user_session'], 3600000);
                } else if ($authInfo['user_session'] == '') {
                    $user_session = D('User', 'Logic')->genHash($authInfo);
                    cookie('user_session', $user_session, 3600000);
                }
            }
            // 缓存访问权限
            RBAC::saveAccessList();

            $log['log_user_id'] = $authInfo['user_id'];
            $log['log_user_name'] = I('post.username');
            $log['log_password'] = encrypt(I('post.password'));
            $log['log_ip'] = get_client_ip();
            $log['log_status'] = 1;

            D('login_log')->data($log)->add();
            // die(D('login_log')->getlastsql());
            $this->success('登录成功！', U("Admin/Index/index"), false);
        };


    }

    public function register()
    {


        $this->display();

    }

    public function registerHandle()
    {

        $new_user_role = get_opinion('new_user_role', true, 5);

        $w = htmlspecialchars(trim($_POST ['username']));
        $i = D('user')->where(array(
            'user_login' => $w
        ))->select();
        if ($i != '') {
            $this->error('用户名已存在！');
        } else {
            // 组合用户信息并添加

            $user = array(
                'user_login' => I('post.username'),
                'user_nicename' => I('post.nickname'),
                'user_pass' => encrypt(I('post.password')),
                'user_email' => I('post.email'),

                'user_status' => 1,

                // 'logintime'=>time(),
                // 'loginip'=>get_client_ip(),
                // 'lock'=>$_POST['lock']
            );
            // 添加用户与角色关系

            $user ['user_level'] = $new_user_role;

            $User = D('User');
            $Role_users = D('Role_users');
            if ($new_id = $User->add($user)) {

                $role = array(
                    'role_id' => $new_user_role,
                    'user_id' => $new_id
                );
                if ($Role_users->add($role)) {
                    $this->success('注册成功！', U('Admin/Access/index'));
                } else {
                    $this->error('注册成功，添加用户权限失败！', U('Admin/Access/index'));
                }
            } else {
                $this->error('注册用户失败！', U('Admin/Access/index'));
            }

        }
        //$this->error("不开放注册");


    }


    /**
     *
     */
    public function forgetpassword()
    {


        $this->display();


    }

    /**
     *
     */
    public function forgetpasswordHandle()
    {
        $verify = new \Think\Verify();

        if (!$verify->check(I('post.vertify'))) {
            $this->error("验证码错误");
        }

        if (IS_POST) {
            $User = D('User', 'Logic');

            $email = I('post.email');

            $user = $User->where(array('user_email' => $email))->find();
            if (!$user) {
                $this->error("不存在用户");
            }

            $new_pass = encrypt($user['user_session']);
            $User->where(array('user_email' => $email))->data(array('user_pass' => $new_pass))->save();


            $res = send_mail($email, "", "用户密码重置", "新密码: " . $user['user_session']); //

            if ($res) {
                $this->success("新密码的邮件已经发送到注册邮箱");
            } else {
                $this->error("请检查邮件发送设置");

            }
        }


    }

    /**
     *
     */
    public function logout()
    {
        $User = D('User', 'Logic');
        $authInfo = $User->detail(session(C('ADMIN_AUTH_KEY')));

        $greencms_hash = $User->genHash($authInfo);
        cookie('user_session', null);

        session_unset();
        session_destroy();

        $this->success('退出成功！', U('Admin/Login/index'), false);
    }
}