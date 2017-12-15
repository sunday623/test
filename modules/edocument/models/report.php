<?php
/**
 * @filesource modules/edocument/models/report.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Report;

/**
 * โมเดลสำหรับอ่านข้อมูลประวัติการดาวน์โหลด (report.php)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลเอกสารที่เลือก
   *
   * @param int $id ID
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function get($id)
  {
    // Model
    $model = new static;
    return $model->db()->createQuery()
        ->from('edocument')
        ->where(array('id', $id))
        ->first();
  }

  /**
   * อ่านข้อมูลประวัติการดาวน์โหลดใส่ลงในตาราง
   *
   * @param int $id ID
   * @return \static
   */
  public static function toDataTable($id)
  {
    // Model
    $model = new static;
    return $model->db()->createQuery()
        ->select('D.id', 'U.status', 'U.name', 'D.last_update', 'D.downloads')
        ->from('edocument_download D')
        ->join('user U', 'LEFT', array('U.id', 'D.member_id'))
        ->where(array('D.document_id', $id));
  }
}