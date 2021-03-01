<?php
/**
 * @filesource modules/index/models/language.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Language;

use Kotchasan\Language;

/**
 * โมเดลสำหรับภาษา (language.php).
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อัปเดตไฟล์ ภาษา
     *
     * @param Database $db
     *
     * @return string
     */
    public static function updateLanguageFile($db)
    {
        // ภาษาที่ติดตั้ง
        $languages = Language::installedLanguage();
        // query ข้อมูลภาษา
        $query = $db->createQuery()->select()->from('language')->order('key');
        // เตรียมข้อมูล
        $datas = array();
        foreach ($query->toArray()->execute() as $item) {
            $save = array('key' => $item['key']);
            foreach ($languages as $lng) {
                if (isset($item[$lng]) && $item[$lng] != '') {
                    if ($item['type'] == 'array') {
                        $data = @unserialize($item[$lng]);
                        if (is_array($data)) {
                            $save[$lng] = $data;
                        }
                    } elseif ($item['type'] == 'int') {
                        $save[$lng] = (int) $item[$lng];
                    } else {
                        $save[$lng] = $item[$lng];
                    }
                }
            }
            $datas[$item['js'] == 1 ? 'js' : 'php'][] = $save;
        }
        // บันทึกไฟล์ภาษา
        $error = '';
        foreach ($datas as $type => $items) {
            $error .= Language::save($items, $type);
        }

        return $error;
    }
}
