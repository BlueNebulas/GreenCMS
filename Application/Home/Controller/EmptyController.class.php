<?php
/**
 * Created by Green Studio.
 * File: EmptyController.php
 * User: TianShuo
 * Date: 14-4-6
 * Time: 下午8:43
 */
/**
 * Created by Green Studio.
 * File: EmptyController.php
 * User: TianShuo
 * Date: 14-4-6
 * Time: 下午8:43
 */

namespace Home\Controller;


/**
 * 空控制器当访问出错时调用
 * Class EmptyController
 * @package Home\Controller
 */
class EmptyController extends HomeBaseController
{
    /**
     * 空控制器实现
     *  @param null
     */
    public function _empty()
    {
        $this->error404();

    }

}