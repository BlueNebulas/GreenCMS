<?php
/**
 * Created by Green Studio.
 * File: HomeBaseController.class.php
 * User: TianShuo
 * Date: 14-1-11
 * Time: 下午1:48
 */

namespace Zel\Controller;

use Common\Controller\BaseController;
use Think\Hook;
use Common\Util\File;

/**
 * Zel模块基础类控制器
 * Class HomeBaseController
 * @package Zel\Controller
 */
abstract class HomeBaseController extends BaseController
{

    /**
     * Zel模块基础类控制器构造
     */
    function __construct()
    {
        parent::__construct();

        $this->customConfig();

    }


    /**
     * 判断变量是否为空为空输出404页
     * @function 是否为空
     *
     * @param $info
     * @param string $message
     */
    public function if404($info, $message = "")
    {
        Hook::listen('home_if404');

        if (empty($info)) $this->error404($message);
    }


    /**
     * 显示404页
     * @function 404 ERROR 需要显示错误的信息
     *
     * @param string $message
     */
    public function error404($message = "非常抱歉，你需要的页面暂时不存在，可能它已经躲起来了。.")
    {
        Hook::listen('home_error404');

        $this->assign("message", $message);

        if (File::file_exists(T('Zel@Index/404'))) {
            $this->display('Index/404');
        } else {
            $this->error('缺少对应的模版而不能显示', U('Zel/Index/index'));
        }

        Hook::listen('app_end');
        die();
    }


}