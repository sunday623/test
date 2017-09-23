<?php
/**
 * @filesource modules/school/models/teacher.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Teacher;

/**
 * เพิ่ม/แก้ไข ข้อมูลนักเรียน
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
  private $datas = array();

  /**
   * อ่านรายชื่อครูจากฐานข้อมูล
   *
   * @return \static
   */
  public static function init()
  {
    $model = new static;
    $query = $model->db()->createQuery()
      ->select('P.id', 'U.name')
      ->from('personnel P')
      ->join('user U', 'INNER', array('U.id', 'P.id'))
      ->where(array('U.active', 1))
      ->toArray()
      ->cacheOn();
    foreach ($query->execute() as $item) {
      $model->datas[$item['id']] = $item['name'];
    }
    return $model;
  }

  /**
   * คืนค่ารายชื่อครูใส่ลงใน select
   *
   * @param int $can_manage_course 0 คืนค่าทุกคน, > 0 คืนค่ารายการที่เลือก
   * @return array
   */
  public function toSelect($can_manage_course)
  {
    $datas = array();
    foreach ($this->datas as $i => $name) {
      if ($can_manage_course == 0 || $i == $can_manage_course) {
        $datas[$i] = $name;
      }
    }
    return $datas;
  }

  /**
   * อ่านชื่อครูที่ $id
   * ถ้าไม่พบคืนค่าว่าง
   *
   * @param int $id
   * @return string
   */
  public function get($id)
  {
    return isset($this->datas[$id]) ? $this->datas[$id] : '';
  }
}