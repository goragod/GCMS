<?php
/**
 * @filesource modules/index/controllers/tags.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Tags;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=tags.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * รายการ Tags.
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ข้อความ title bar
        $this->title = Language::trans('{LNG_List of} {LNG_Tags}');
        // เมนู
        $this->menu = 'widgets';
        // สามารถตั้งค่าระบบได้
        if (Login::checkPermission(Login::adminAccess(), 'can_config')) {
            // แสดงผล
            $section = Html::create('section');
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-widgets">{LNG_Widgets}</span></li>');
            $ul->appendChild('<li><span>{LNG_Tags}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-tags">'.$this->title().'</h2>',
            ));
            // แสดงตาราง
            $section->appendChild(createClass('Index\Tags\View')->render($request));

            return $section->render();
        }
        // 404.html

        return \Index\Error\Controller::page404();
    }
}
