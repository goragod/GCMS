<?php
/**
 * @filesource modules/index/controllers/member.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Member;

use Kotchasan\Http\Request;

/**
 * Controller หลัก สำหรับแสดง frontend ของ GCMS.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{
    /**
     * แสดงผลฟอร์ม ที่เรียกมาจาก GModal.
     *
     * @param Request $request
     */
    public function modal(Request $request)
    {
        $action = $request->post('action')->toString();
        if ($action === 'register') {
            $page = createClass('Index\Register\View')->render($request, true);
        } elseif ($action === 'forgot') {
            $page = createClass('Index\Forgot\View')->render($request, true);
        } elseif ($action === 'login') {
            $page = createClass('Index\Dologin\View')->render($request);
        } else {
            // 404
            $page = createClass('Index\Error\Controller')->init('index');
        }
        echo json_encode($page);
    }

    /**
     * @param Request $request
     */
    public function editprofile(Request $request)
    {
        return createClass('Index\Editprofile\View')->render($request);
    }

    /**
     * @param Request $request
     */
    public function sendmail(Request $request)
    {
        return createClass('Index\Sendmail\View')->render($request);
    }

    /**
     * @param Request $request
     */
    public function register(Request $request)
    {
        return createClass('Index\Register\View')->render($request, false);
    }

    /**
     * @param Request $request
     */
    public function forgot(Request $request)
    {
        return createClass('Index\Forgot\View')->render($request);
    }

    /**
     * @param Request $request
     */
    public function dologin(Request $request)
    {
        return createClass('Index\Dologin\View')->render($request);
    }

    /**
     * @param Request $request
     */
    public function member(Request $request)
    {
        return createClass('Index\View\View')->render($request);
    }

    /**
     * @param Request $request
     */
    public function activate(Request $request)
    {
        return createClass('Index\Activate\View')->render($request);
    }
}
