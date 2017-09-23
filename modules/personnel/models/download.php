<?php
/**
 * @filesource modules/personnel/models/download.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Download;

use \Kotchasan\Language;

/**
 * เพิ่ม/แก้ไข ข้อมูลบุคลากร.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * Query ข้อมูลบุคลากรสำหรับการดาวน์โหลด
   *
   * @param array $params
   * @return array
   */
  public static function getAll($params)
  {
    $where = array();
    if (isset($params['active']) && ($params['active'] === 1 || $params['active'] === 0)) {
      $where[] = array('U.active', $params['active']);
    }
    $select = array('U.name', 'P.id_card', 'U.phone', 'P.custom');
    // หมวดหมู่ของบุคลากร
    foreach (Language::get('PERSONNEL_CATEGORY') as $k => $v) {
      if (!empty($params[$k])) {
        $where[] = array("P.{$k}", $params[$k]);
      }
      $select[] = "P.{$k}";
    }
    // Model
    $model = new static;
    // Query
    $query = $model->db()->createQuery()
      ->select($select)
      ->from('personnel P')
      ->join('user U', 'INNER', array('U.id', 'P.id'))
      ->order(array('P.position', 'P.order'))
      ->toArray();
    if (!empty($where)) {
      $query->where($where);
    }
    return $query->execute();
  }
}