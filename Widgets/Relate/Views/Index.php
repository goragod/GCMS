<?php
/**
 * @filesource Widgets/Relate/Views/Index.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Widgets\Relate\Views;

use Document\Index\Controller;
use Kotchasan\Date;
use Kotchasan\Grid;
use Kotchasan\Language;

/**
 * Controller หลัก สำหรับแสดงผล Widget.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Gcms\View
{
    /**
     * แสดงผล Widget.
     *
     * @param object $index
     *
     * @return string
     */
    public static function render($index)
    {
        // ชื่อโมดูล
        $module = \Index\Module\Model::getModuleWithConfig('document', '', $index->module_id);
        if ($module) {
            // /document/relateitem.html
            $listitem = Grid::create('document', $module->module, 'relateitem');
            // คอลัมน์
            $listitem->setCols($index->cols);
            // ลิสต์รายการ
            foreach ($index->items as $item) {
                if (!empty($item->picture) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$item->picture)) {
                    $thumb = WEB_URL.DATA_FOLDER.'document/'.$item->picture;
                } elseif (!empty($index->icon) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$index->icon)) {
                    $thumb = WEB_URL.DATA_FOLDER.'document/'.$index->icon;
                } else {
                    $thumb = WEB_URL.(isset($index->default_icon) ? $index->default_icon : 'modules/document/img/document-icon.png');
                }
                $listitem->add(array(
                    '/{URL}/' => Controller::url($module->module, $item->alias, $item->id),
                    '/{TOPIC}/' => $item->topic,
                    '/{DATE}/' => Date::format($item->create_date, 'd M Y'),
                    '/{COMMENTS}/' => number_format($item->comments),
                    '/{VISITED}/' => number_format($item->visited),
                    '/{DETAIL}/' => $item->description,
                    '/{PICTURE}/' => $thumb,
                    '/{COLS}/' => $index->cols,
                ));
            }

            if ($listitem->hasItem()) {
                return createClass('Gcms\View')->renderHTML($listitem->render());
            } else {
                return Language::trans('<div class="error center">{LNG_Sorry, no information available for this item.}</div>');
            }
        }
    }
}
