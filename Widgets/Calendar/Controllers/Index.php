<?php
/**
 * @filesource Widgets/Calendar/Controllers/Index.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Widgets\Calendar\Controllers;

use Gcms\Gcms;

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
            $module = $index->module;
            $owner = $index->owner;
        } else {
            $module = '';
            $owner = 'document';
        }
        $calendar = array(
            '<div id=widget-calendar></div>',
            '<script>initWidgetCalendar("widget-calendar", "'.$owner.'", "'.$module.'");</script>',
        );

        return implode('', $calendar);
    }
}
