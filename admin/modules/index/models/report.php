<?php
/**
 * @filesource modules/index/models/report.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Report;

use Kotchasan\Database\Sql;

/**
 * อ่านข้อมูลการเยี่ยมชมในวันที่เลือก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านข้อมูลการเยี่ยมชมในวันที่เลือก
     *
     * @param array $params
     *
     * @return array
     */
    public static function get($params)
    {
        $where = array(
            array(Sql::DATE('time'), $params['date']),
        );
        if ($params['h'] > -1) {
            $where[] = array(Sql::HOUR('time'), $params['h']);
        }
        if ($params['ip'] != '') {
            $where[] = array('ip', $params['ip']);
        }
        $query = static::createQuery()
            ->from('logs')
            ->where($where);
        if ($params['ip'] == '') {
            $query->select('time', 'ip', Sql::COUNT('*', 'count'), 'url', 'referer', 'user_agent')
                ->groupBy('session_id', 'referer');
        } else {
            $query->select('time', 'ip', 'url', 'referer', 'user_agent');
        }

        return $query;
    }

    /**
     * คืนค่าจำนวน log รายชั่วโมง ตามวันที่เลือก
     *
     * @param string $date
     *
     * @return array
     */
    public static function logPerHour($date)
    {
        $query = static::createQuery()
            ->select(Sql::HOUR('time', 'hour'), Sql::COUNT('*', 'count'))
            ->from('logs')
            ->where(array(Sql::DATE('time'), $date))
            ->groupBy('hour')
            ->cacheOn();
        $result = array();
        foreach ($query->execute() as $item) {
            $result[$item->hour] = $item->count;
        }

        return $result;
    }
}
