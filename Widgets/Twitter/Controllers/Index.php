<?php
/**
 * @filesource Widgets/Twitter/Controllers/Index.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Widgets\Twitter\Controllers;

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
        if (empty(self::$cfg->twitter)) {
            self::$cfg->twitter = \Widgets\Twitter\Models\Settings::defaultSettings();
        }
        foreach (self::$cfg->twitter as $key => $value) {
            if (!isset($query_string[$key])) {
                $query_string[$key] = $value;
            }
        }

        return \Widgets\Twitter\Views\Index::render($query_string);
    }
}
