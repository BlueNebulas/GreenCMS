<?php
/**
 * Created by Green Studio.
 * File: HomeBaseController.class.php
 * User: TianShuo
 * Date: 14-1-11
 * Time: 下午1:48
 */

namespace Home\Controller;
use Common\Controller\BaseController;

/**
 * Class HomeBaseController
 * @package Home\Controller
 */
class HomeBaseController extends BaseController
{

    /**
     * 构造
     */
    function __construct()
    {
        parent::__construct();

        $this->newPosts = D('Posts', 'Logic')->getList(5, 'single', 'post_date desc', false);
        $this->friendurl = D('Links', 'Logic')->getList(5);

        $this->customConfig();
    }


    /**
     * @function 是否为空
     * @param $info
     * @param string $message
     */
    public function if404($info, $message = "")
    {
        if (empty($info)) $this->error404($message);
    }


    /**
     * @function 404 ERROR
     * @param string $message
     */
    public function error404($message = "")
    {
        $this->assign("message", $message);
        $this->display('Index/404');
        die();
    }


}