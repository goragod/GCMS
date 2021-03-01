<?php
/**
 * @filesource Gcms/Login.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Gcms;

use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * คลาสสำหรับตรวจสอบการ Login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Login extends \Kotchasan\Login
{
    /**
     * ตรวจสอบความสามารถในการเข้าระบบแอดมิน
     * คืนค่าข้อมูลสมาชิก (แอเรย์) ถ้าสามารถเข้าระบบแอดมินได้ ไม่ใช่คืนค่า null.
     *
     * @return array|null
     */
    public static function adminAccess()
    {
        $login = self::isMember();
        return isset($login['active']) && $login['active'] == 1 ? $login : null;
    }

    /**
     * ฟังก์ชั่นตรวจสอบการ login และบันทึกการเข้าระบบ
     * เข้าระบบสำเร็จคืนค่าแอเรย์ข้อมูลสมาชิก, ไม่สำเร็จ คืนค่าข้อความผิดพลาด
     *
     * @param array $params ข้อมูลการ login ที่ส่งมา $params = array('username' => '', 'password' => '');
     *
     * @return string|array
     */
    public function checkLogin($params)
    {
        // ตรวจสอบสมาชิกกับฐานข้อมูล
        $login_result = self::checkMember($params);
        if (is_array($login_result)) {
            // ip ที่ login
            $ip = self::$request->getClientIp();
            // current session
            $session_id = session_id();
            // ลบ password
            unset($login_result['password']);
            // เวลานี้
            $mktime = time();
            if (self::$cfg->member_only || empty($login_result['token']) || $mktime - $login_result['lastvisited'] > 86400) {
                // อัปเดต token
                $login_result['token'] = \Kotchasan\Password::uniqid(40);
                $save = array('token' => $login_result['token']);
            }
            if ($session_id != $login_result['session_id']) {
                // อัปเดตการเยี่ยมชม
                ++$login_result['visited'];
                $save = array(
                    'session_id' => $session_id,
                    'visited' => $login_result['visited'],
                    'lastvisited' => $mktime,
                    'ip' => $ip,
                    'token' => $login_result['token'],
                );
            }
            if (!empty($save)) {
                // บันทึกการเข้าระบบ
                \Kotchasan\Model::createQuery()
                    ->update('user')
                    ->set($save)
                    ->where((int) $login_result['id'])
                    ->execute();
            }
        }
        return $login_result;
    }

    /**
     * ฟังก์ชั่นตรวจสอบสมาชิกกับฐานข้อมูล
     * คืนค่าข้อมูลสมาชิก (array) ไม่พบคืนค่าข้อความผิดพลาด (string)
     *
     * @param array $params
     *
     * @return array|string
     */
    public static function checkMember($params)
    {
        // query Where
        $where = array();
        foreach (self::$cfg->login_fields as $field) {
            $where[] = array("U.{$field}", $params['username']);
        }
        $query = \Kotchasan\Model::createQuery()
            ->select('U.*')
            ->from('user U')
            ->where($where, 'OR')
            ->order('U.status DESC')
            ->toArray();
        $login_result = null;
        foreach ($query->execute() as $item) {
            if (isset($params['password']) && $item['password'] === sha1(self::$cfg->password_key.$params['password'].$item['salt'])) {
                // ตรวจสอบรหัสผ่าน
                $login_result = $item;
                break;
            } elseif (isset($params['token']) && $params['token'] === $item['token']) {
                // ตรวจสอบ token
                $login_result = $item;
                break;
            }
        }
        if ($login_result === null) {
            // ตรวจสอบกับ API
            $login_result = self::apiAuthentication($params, isset($item) ? $item : null);
            if (is_array($login_result)) {
                // คืนค่าข้อมูลสมาชิก
                return $login_result;
            } elseif (isset($item)) {
                // password ไม่ถูกต้อง
                self::$login_input = 'password';
                return Language::replace('Invalid :name', array(':name' => Language::get('Password')));
            } else {
                // user ไม่ถูกต้อง
                self::$login_input = 'username';
                return Language::get('not a registered user');
            }
        } elseif (!empty($login_result['activatecode'])) {
            // ยังไม่ได้ activate
            self::$login_input = 'username';
            return Language::get('No confirmation email, please check your e-mail');
        } elseif (!empty($login_result['ban'])) {
            // ติดแบน
            self::$login_input = 'username';
            return Language::get('Members were suspended');
        } else {
            // permission
            $login_result['permission'] = empty($login_result['permission']) ? array() : explode(',', trim($login_result['permission'], " \t\n\r\0\x0B,"));
            // คืนค่าข้อมูลสมาชิก
            return $login_result;
        }
    }

    /**
     * ตรวจสอบข้อมูลกับ API
     * คืนค่าแอเรย์ ถ้าสำเร็จ
     * ไม่สำเร็จคืนค่า null
     *
     * @param array $params
     * @param array $user
     *
     * @return array|null
     */
    private static function apiAuthentication($params, $user)
    {
        if (
            !empty(self::$cfg->api_secret) &&
            !empty(self::$cfg->api_token) &&
            !empty(self::$cfg->api_url) &&
            !empty($params['username']) &&
            !empty($params['password']) &&
            stripos(self::$cfg->api_url, HOST) === false
        ) {
            // ตรวจสอบกับ API
            $login_result = \Gcms\Api::login($params['username'], $params['password']);
            if (is_array($login_result) && isset($login_result['code']) && $login_result['code'] == 0) {
                if ($user === null) {
                    // login ผ่าน API สำเร็จ ลงทะเบียน user ใหม่
                    $model = \Kotchasan\Model::create();
                    // Database
                    $db = $model->db();
                    // Table
                    $user_table = $model->getTableName('user');
                    // สมาชิกใหม่
                    if (!empty($login_result['phone'])) {
                        // ตรวจสอบ phone ซ้ำ
                        $phone = $db->first($user_table, array('phone1', $login_result['phone']));
                    } else {
                        $phone = false;
                    }
                    if ($phone === false) {
                        // ตรวจสอบชื่อเรียก
                        if (!empty($login_result['displayname'])) {
                            $a = 1;
                            $displayname = $login_result['displayname'];
                            while (true) {
                                if (false === $db->first($user_table, array('displayname', $login_result['displayname']))) {
                                    break;
                                } else {
                                    ++$a;
                                    $login_result['displayname'] = $displayname.$a;
                                }
                            }
                        }
                        $login_result = array(
                            'email' => $login_result['email'],
                            'name' => $login_result['name'],
                            'displayname' => $login_result['displayname'],
                            'phone1' => $login_result['phone'],
                            'permission' => '',
                            'status' => self::$cfg->new_register_status,
                            'active' => 0,
                            'social' => 0,
                            'visited' => 1,
                            'ip' => self::$request->getClientIp(),
                            'lastvisited' => time(),
                            'country' => 'TH',
                            'salt' => \Kotchasan\Password::uniqid(),
                            'session_id' => session_id(),
                            'token' => \Kotchasan\Password::uniqid(40),
                        );
                        $login_result['password'] = sha1(self::$cfg->password_key.$params['password'].$login_result['salt']);
                        $login_result['create_date'] = $login_result['lastvisited'];
                        // register
                        $login_result['id'] = $db->insert($user_table, $login_result);
                        // permission
                        $login_result['permission'] = array();
                        // คืนค่าข้อมูล login
                        return $login_result;
                    }
                } else {
                    // permission
                    $user['permission'] = empty($user['permission']) ? array() : explode(',', trim($user['permission'], " \t\n\r\0\x0B,"));
                    // คืนค่าข้อมูลจากฐานข้อมูลสมาชิกที่พบ
                    return $user;
                }
            }
        }
        return null;
    }

    /**
     * ตรวจสอบความสามารถในการตั้งค่า
     * แอดมินสูงสุด (status=1) ทำได้ทุกอย่าง
     * คืนค่าข้อมูลสมาชิก (แอเรย์) ถ้าไม่สามารถทำรายการได้คืนค่า null.
     *
     * @param array        $login
     * @param array|string $permission
     *
     * @return array|null
     */
    public static function checkPermission($login, $permission)
    {
        if (!empty($login)) {
            if ($login['status'] == 1) {
                // แอดมิน
                return $login;
            } elseif (!empty($permission)) {
                if (is_array($permission)) {
                    foreach ($permission as $item) {
                        if (in_array($item, $login['permission'])) {
                            // มีสิทธิ์
                            return $login;
                        }
                    }
                } elseif (in_array($permission, $login['permission'])) {
                    // มีสิทธิ์
                    return $login;
                }
            }
        }
        // ไม่มีสิทธิ
        return null;
    }

    /**
     * ฟังก์ชั่นส่งอีเมลลืมรหัสผ่าน
     *
     * @param Request $request
     *
     * @return void
     */
    public function forgot(Request $request)
    {
        // ค่าที่ส่งมา
        $username = $request->post('login_username')->url();
        if (empty($username)) {
            if ($request->post('action')->toString() === 'forgot') {
                self::$login_message = Language::get('Please fill in');
            }
        } else {
            self::$login_params['username'] = $username;
            // ชื่อฟิลด์สำหรับตรวจสอบอีเมล ใช้ฟิลด์แรกจาก config
            $field = reset(self::$cfg->login_fields);
            // Model
            $model = new \Kotchasan\Model();
            // ตาราง user
            $table = $model->getTableName('user');
            // Database
            $db = $model->db();
            // ค้นหา username
            $search = $db->first($table, array(
                array($field, $username),
                array('social', 0),
            ));
            if ($search === false) {
                self::$login_message = Language::get('not a registered user');
            } else {
                // รหัสผ่านใหม่
                $password = \Kotchasan\Password::uniqid(6);
                // ข้อมูลอีเมล
                $replace = array(
                    '/%PASSWORD%/' => $password,
                    '/%EMAIL%/' => $search->$field,
                );
                // send mail
                $err = \Gcms\Email::send(3, 'member', $replace, $search->$field);
                if ($err->error()) {
                    // ไม่สำเร็จ
                    self::$login_message = $err->getErrorMessage();
                } else {
                    // อัปเดตรหัสผ่านใหม่
                    $salt = \Kotchasan\Password::uniqid();
                    $save = array(
                        'salt' => $salt,
                        'password' => sha1(self::$cfg->password_key.$password.$salt),
                    );
                    $db->update($table, (int) $search->id, $save);
                    // คืนค่า
                    self::$login_message = Language::get('Your message was sent successfully');
                    self::$request = $request->withQueryParams(array('action' => 'login'));
                }
            }
        }
    }

    /**
     * ฟังก์ชั่นตรวจสอบว่า เป็นสมาชิกตัวอย่างหรือไม่
     * คืนค่าข้อมูลสมาชิก (แอเรย์) ถ้าไม่ใช่สมาชิกตัวอย่าง, null ถ้าเป็นสมาชิกตัวอย่างและเปิดโหมดตัวอย่างไว้.
     *
     * @param array|null $login
     *
     * @return array|null
     */
    public static function notDemoMode($login)
    {
        return $login && !empty($login['social']) && self::$cfg->demo_mode ? null : $login;
    }
}
