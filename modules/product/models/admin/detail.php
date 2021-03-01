<?php
/**
 * @filesource modules/product/models/admin/detail.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Product\Admin\Detail;

/**
 * ตาราง product_detail.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Orm\Field
{
    /**
     * ชื่อตาราง.
     *
     * @var string
     */
    protected $table = 'product_detail D';
}
