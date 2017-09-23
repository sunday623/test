<?php
/**
 * @filesource modules/personnel/models/user.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\User;

/**
 * ตารางสมาชิก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * Query ข้อมูลบุคลากรสำหรับส่งให้กับ DataTable
   *
   * @param array $login
   * @return /static
   */
  public static function toDataTable()
  {
    $model = new static;
    return $model->query();
  }

  /**
   * query ข้อมูลบุคลากร
   *
   * @return QueryBuilder
   */
  private function query()
  {
    return $this->db()->createQuery()
        ->select('P.*', 'U.name', 'U.picture', 'U.active')
        ->from('personnel P')
        ->join('user U', 'INNER', array('U.id', 'P.id'));
  }

  /**
   * อ่านข้อมูลบุคลากรที่ $id
   *
   * @param int $id
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function get($id)
  {
    $model = new static;
    return $model->query()
        ->where(array('P.id', $id))
        ->first('P.*', 'U.name', 'U.birthday', 'U.phone', 'U.sex', 'U.picture', 'U.permission');
  }

  /**
   * อ่านข้อมูลรายการที่เลือกสำหรับหน้า write.php
   *
   * @param int $id 0 หมายถึงรายการใหม่, > 0 รายการที่ต้องการ
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function getForWrite($id)
  {
    if (empty($id)) {
      return (object)array(
          'id' => 0,
          'name' => '',
          'id_card' => '',
          'phone' => '',
          'order' => '',
          'custom' => array(),
          'birthday' => ''
      );
    } else {
      $model = new static;
      $search = $model->query()
        ->where(array('P.id', $id))
        ->first('P.*', 'U.name', 'U.birthday', 'U.phone', 'U.sex', 'U.picture', 'U.permission');
      if ($search) {
        $search->custom = @unserialize($search->custom);
        if (!is_array($search->custom)) {
          $search->custom = array();
        }
      }
      return $search;
    }
  }

  /**
   * ตรวจสอบเลขประจำตัวประชาชนซ้ำ
   *
   * @param int $id
   * @param array $personnel
   * @return boolean true ถ้ามีแล้วแต่ไม่ใช่ ID ตัวเอง
   */
  public static function exists($id, $personnel)
  {
    if ($personnel['id_card'] == '') {
      // ไม่มีข้อมูลต้องตรวจสอบ
      return false;
    } else {
      $model = new static;
      $search = $model->db()->createQuery()
        ->from('personnel')
        ->where(array('id_card', $personnel['id_card']))
        ->toArray()
        ->first('id');
      return $search !== false && $search['id'] != $id;
    }
  }
}