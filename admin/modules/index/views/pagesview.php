<?php
/**
 * @filesource modules/index/views/pagesview.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Pagesview;

/**
 * ฟอร์ม forgot.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\Adminview
{
    /**
     * @param  $date
     *
     * @return mixed
     */
    public function render($date)
    {
        $total = 0;
        $thead = array();
        $tbody = array();
        $list = \Index\Pagesview\Model::get($date);
        $l = count($list);
        foreach ($list as $i => $item) {
            list($y, $m, $d) = explode('-', $item['date']);
            $d = '<a href="index.php?module=report&amp;date='.$item['date'].'">'.(int) $d.'</a>';
            $c = $i > $l - 13 ? ($i > $l - 7 ? '' : 'mobile') : 'tablet';
            $thead[] = '<th class='.$c.'>'.$d.'</th>';
            $tbody[] = '<td class='.$c.'>'.number_format($item['pages_view']).'</td>';
            $total = $total + $item['pages_view'];
        }
        $content = '<section class="section margin-top">';
        $content .= '<div id=pageview_graph class=ggraphs>';
        $content .= '<canvas></canvas>';
        $content .= '<table class="data fullwidth">';
        $content .= '<thead><tr><th>{LNG_date}</th>'.implode('', $thead).'</tr></thead>';
        $content .= '<tbody><tr><th>{LNG_Pages view}</th>'.implode('', $tbody).'</tr></tbody>';
        $content .= '</table>';
        $content .= '</div>';
        $content .= '</section>';
        $content .= '<script>new GGraphs("pageview_graph", {type:"bar"});</script>';

        return $content;
    }
}
