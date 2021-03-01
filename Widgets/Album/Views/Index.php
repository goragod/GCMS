<?php
/**
 * @filesource Widgets/Album/Views/Index.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Widgets\Album\Views;

use Gallery\Index\Controller;

/**
 * Controller หลัก สำหรับแสดงผล Widget.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\View
{
    /**
     * แสดงผล Widget.
     *
     * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
     *
     * @return string
     */
    public static function render($query_string)
    {
        $dir = DATA_FOLDER.'gallery/';
        $widget = array();
        foreach (\Widgets\Album\Models\Index::get($query_string) as $item) {
            $img = is_file(ROOT_PATH.$dir.$item->id.'/'.$item->image) ? WEB_URL.$dir.$item->id.'/thumb_'.$item->image : WEB_URL.'modules/gallery/img/noimage.jpg';
            $url = Controller::url($item->module, $item->id);
            $widget[] = '<div class=col'.$query_string['cols'].'><div class=figure>';
            $widget[] = '<a href="'.$url.'"><img src="'.$img.'" class=nozoom alt="'.$item->topic.'"></a>';
            $widget[] = '<a class=figcaption href="'.$url.'" title="'.$item->topic.'"><span class=cuttext>'.$item->topic.'</span></a>';
            $widget[] = '</div></div>';
        }
        if (!empty($widget)) {
            return '<div class="widget-album document-list thumbview">'.implode('', $widget).'</div>';
        }
    }
}
