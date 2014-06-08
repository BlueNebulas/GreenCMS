<?php
/**
 * Created by Green Studio.
 * File: IndexController.class.php
 * User: TianShuo
 * Date: 14-1-25
 * Time: 上午10:38
 */

namespace Admin\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class IndexController extends AdminBaseController
{
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
    public function main()
    {
        $this->redirect(getURL('Index/index'));
    }


    public function checkVersion()
    {
        $UpdateEvent = new \Common\Event\UpdateEvent();
        $cheack_res = $UpdateEvent->check();

        if ($cheack_res) {
            $message =
                '<li><a href="' . U("Admin/System/update") . '"><i class="fa fa-laptop"></i> 发现新的可升级版本</a></li>';
        } else {
            $message = 'none';
        }

        die($message);
    }


    public function ajaxCron()
    {
        die('ok');

    }


    /**
     *
     */
    public function changePass()
    {
        $this->display('changepass');
    }

    /**
     *
     */
    public function changepassHandle()
    {

        if (I('post.password') != I('post.rpassword')) {
            $this->error('两次密码不同');
        }

        $User = D('User', 'Logic');

        $user = $User->detail((int)$_SESSION [C('USER_AUTH_KEY')]);
        if ($user['user_pass'] != encrypt(I('post.opassword'))) {
            $this->error("原用户密码不正确");
        }

        $User->user_id = (int)$_SESSION [C('USER_AUTH_KEY')];
        $User->user_pass = encrypt($_POST['password']);

        if ($User->save()) {
            $this->success('密码修改成功', U("Admin/Login/logout"), false);
        } else {
            $this->error('密码修改失败');
        }
    }


    public function profile()
    {

        $uid= ( int )$_SESSION [C('USER_AUTH_KEY')];
        $user = D('User', 'Logic')->detail($uid);
        $this->assign('user', $user);
        $this->assign('action', '用户档案');

        $this->display();


    }



    public function sns(){
        $this->display();

    }


}