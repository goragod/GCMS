<?php
/**
 * @filesource Widgets/Document/Controllers/Index.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Widgets\Document\Controllers;

use Gcms\Gcms;
use Kotchasan\Grid;
use Kotchasan\Http\Request;
use Kotchasan\Template;

/**
 * Controller หลัก สำหรับแสดงผล Widget.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Controller
{
    /**
     * แสดงผล Widget.
     *
     * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
     *
     * @return string
     */
    public function get($query_string)
    {
        if (!empty(Gcms::$module) && !empty($query_string['module']) && $index = Gcms::$module->findByModule($query_string['module'])) {
            if ($index->owner == 'document') {
                // ค่าที่ส่งมา
                $cols = isset($query_string['cols']) ? (int) $query_string['cols'] : 1;
                if (isset($query_string['count'])) {
                    $rows = ceil($query_string['count'] / $cols);
                } elseif (isset($query_string['rows'])) {
                    $rows = (int) $query_string['rows'];
                }
                if (empty($rows)) {
                    $rows = ceil((int) $index->news_count / $cols);
                }
                if ($rows > 0 && $cols > 0) {
                    $cat = isset($query_string['cat']) ? $query_string['cat'] : 0;
                    $interval = isset($query_string['interval']) ? (int) $query_string['interval'] : 0;
                    $sort = isset($query_string['sort']) ? (int) $query_string['sort'] : $index->news_sort;
                    $show = isset($query_string['show']) && preg_match('/^[a-z0-9]+$/', $query_string['show']) ? $query_string['show'] : '';
                    $style = isset($query_string['style']) && in_array($query_string['style'], array('listview', 'iconview', 'thumbview')) ? $query_string['style'] : 'listview';
                    // widget.html
                    $template = Template::create('document', $index->module, 'widget');
                    $template->add(array(
                        '/{DETAIL}/' => '<script>getWidgetNews("{ID}", "Document", '.$interval.')</script>',
                        // module_id_cat_rows_cols_sort_show
                        '/{ID}/' => uniqid().'_'.$index->module_id.'_'.$cat.'_'.$rows.'_'.$cols.'_'.$sort.'_'.$show,
                        '/{MODULE}/' => $index->module,
                        '/{STYLE}/' => $style,
                    ));

                    return $template->render();
                }
            }
        }

        return '';
    }

    /**
     * อ่านข้อมูลจาก Ajax.
     *
     * @param Request $request
     *
     * @return string
     */
    public function getWidgetNews(Request $request)
    {
        // module_id_cat_rows_cols_sort_show
        if ($request->isReferer() && preg_match('/^([a-z0-9]+)_([0-9]+)_([0-9,]{0,})_([0-9]+)_([0-9]+)_([0-9]+)_([a-z]{0,})$/', $request->post('id')->toString(), $match)) {
            $rows = (int) $match[4];
            $cols = (int) $match[5];
            // ตรวจสอบโมดูล
            $index = \Index\Module\Model::getModuleWithConfig('document', '', $match[2]);
            if ($index) {
                // widgetitem.html
                $listitem = Grid::create('document', $index->module, 'widgetitem');
                $listitem->setCols($cols);
                // เครื่องหมาย new
                $valid_date = time() - (int) $index->new_date;
                // query ข้อมูล
                foreach (\Widgets\Document\Models\Index::get($match[2], $match[3], $match[7], $match[6], $rows * $cols) as $item) {
                    $listitem->add(\Widgets\Document\Views\Index::renderItem($index, $item, $valid_date, $cols));
                }
                // คืนค่า
                echo createClass('Gcms\View')->renderHTML($listitem->render());
            }
        }
    }
}
