<?php
/**
 * @filesource modules/document/controllers/admin/home.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Document\Admin\Home;

use Gcms\Gcms;
use Kotchasan\Http\Request;

/**
 * ข้อมูลหน้า Home.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{
    /**
     * popular page.
     *
     * @param Request    $request
     * @param Collection $grid
     * @param array      $login
     */
    public static function addGrid(Request $request, $grid, $login)
    {
        if (Gcms::$module->findInstalledOwners('document')) {
            $thead = array();
            $visited = array();
            foreach (\Document\Admin\Home\Model::popularpage() as $item) {
                $thead[] = '<th>'.$item['topic'].'</th>';
                $visited[] = '<td>'.$item['visited_today'].'</td>';
            }
            $content = '<section class=section>';
            $content .= '<header><h3 class=icon-pie>{LNG_Popular daily} ({LNG_Module} Document)</h3></header>';
            $content .= '<div id=visited_graph class=ggraphs>';
            $content .= '<canvas></canvas>';
            $content .= '<table class=hidden>';
            $content .= '<thead><tr><th>&nbsp;</th>'.implode('', $thead).'</tr></thead>';
            $content .= '<tbody>';
            $content .= '<tr><th>{LNG_Visited}</th>'.implode('', $visited).'</tr>';
            $content .= '</tbody>';
            $content .= '</table>';
            $content .= '</div>';
            $content .= '</section>';
            $content .= '<script>';
            $content .= 'new GGraphs("visited_graph", {type:"donut",colors:COLORS,centerX:30+Math.round($G("visited_graph").getHeight()/2),labelOffset:35,centerOffset:30,strokeColor:null});';
            $content .= '</script>';
            \Index\Home\Controller::renderGrid($grid, $content, 6, true, 'tablet-block');
        }
    }
}
