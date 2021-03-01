<?php
/**
 * @filesource modules/index/views/upgrading.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Upgrading;

/**
 * อัปเกรด.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model

{
    /**
     * @param $db
     * @param $version
     *
     * @return object
     */
    public static function upgrade($db, $version)
    {
        if ($version == '9.1.0' || $version == '10.1.2') {
            // อัปเกรดจาก 9.1.0 (เวอร์ชั่นที่ไม่ได้ใช้ Kotchasan)
            return \Index\Upgrade910\Model::upgrade($db);
        } elseif ($version < '11.2.0') {
            // อัปเกรดเป็น 11.2.0
            return \Index\Upgrade1120\Model::upgrade($db);
        } elseif ($version < '12.0.0') {
            // อัปเกรดเป็น 12.0.0
            return \Index\Upgrade1200\Model::upgrade($db);
        } elseif ($version < '13.0.0') {
            // อัปเกรดเป็น 13.0.0
            return \Index\Upgrade1300\Model::upgrade($db);
        } elseif ($version < '13.5.0') {
            // อัปเกรดเป็น 13.5.0
            return \Index\Upgrade1350\Model::upgrade($db);
        } elseif ($version < '13.6.0') {
            // อัปเกรดเป็น 13.6.0
            return \Index\Upgrade1360\Model::upgrade($db);
        }
    }
}
