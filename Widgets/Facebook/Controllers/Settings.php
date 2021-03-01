<?php
/**
 * @filesource Widgets/Facebook/Controllers/Settings.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Widgets\Facebook\Controllers;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * Controller สำหรับจัดการการตั้งค่าเริ่มต้น.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Settings extends \Gcms\Controller
{
    /**
     * แสดงผล.
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        if (defined('MAIN_INIT')) {
            // ข้อความ title bar
            $this->title = Language::trans('{LNG_Configuring} {LNG_Facebook page}');
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
                $ul->appendChild('<li><span>{LNG_Facebook page}</span></li>');
                $section->add('header', array(
                    'innerHTML' => '<h2 class="icon-facebook">'.$this->title().'</h2>',
                ));
                // แสดงฟอร์ม
                $section->appendChild(createClass('Widgets\Facebook\Views\Settings')->render());

                return $section->render();
            }
        }
        // 404.html

        return \Index\Error\Controller::page404();
    }
}
