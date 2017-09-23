<?php
/**
 * @filesource modules/index/models/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Category;

use \Kotchasan\Language;

/**
 * Model สำหรับจัดการหมวดหมู่ต่างๆ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
  private $datas = array();

  /**
   * อ่านรายชื่อหมวดหมู่จากฐานข้อมูลตามภาษาปัจจุบัน
   * สำหรับการแสดงผล
   *
   * @param string $type
   * @return \static
   */
  public static function init($type)
  {
    $obj = new static;
    // ภาษาปัจจุบัน
    $lng = Language::name();
    // อ่านรายชื่อตำแหน่งจากฐานข้อมูล
    foreach (self::generate($type) as $item) {
      $obj->datas[$item['category_id']] = $item[$lng];
    }
    return $obj;
  }

  /**
   * Query ข้อมูลหมวดหมู่จากฐานข้อมูล
   *
   * @param string $type
   * @return array
   */
  public static function generate($type)
  {
    // Model
    $model = new static;
    // Query
    $query = $model->db()->createQuery()
      ->select('id', 'category_id', 'topic')
      ->from('category')
      ->where(array('type', $type))
      ->order('category_id')
      ->toArray()
      ->cacheOn();
    $result = array();
    foreach ($query->execute() as $item) {
      $result[$item['category_id']] = array(
        'id' => $item['id'],
        'category_id' => $item['category_id'],
      );
      $topic = @unserialize($item['topic']);
      foreach (Language::installedLanguage() as $lng) {
        $result[$item['category_id']][$lng] = is_array($topic) && isset($topic[$lng]) ? $topic[$lng] : '';
      }
    }
    return $result;
  }

  /**
   * อ่านหมวดหมู่สำหรับใส่ลงใน DataTable
   * ถ้าไม่มีคืนค่าข้อมูลเปล่าๆ 1 แถว
   *
   * @param string $type
   * @return array
   */
  public static function toDataTable($type)
  {
    // Query ข้อมูลหมวดหมู่จากฐานข้อมูล
    $result = self::generate($type);
    if (empty($result)) {
      $result = array(array('id' => 0, 'category_id' => 1));
      foreach (Language::installedLanguage() as $lng) {
        $result[0][$lng] = '';
      }
    }
    return $result;
  }

  /**
   * ลิสต์รายการหมวดหมู่
   * สำหรับใส่ลงใน select
   *
   * @return array
   */
  public function toSelect()
  {
    return $this->datas;
  }

  /**
   * อ่านหมวดหมู่จาก $category_id
   * ไม่พบ คืนค่าว่าง
   *
   * @param int $category_id
   * @return string
   */
  public function get($category_id)
  {
    return isset($this->datas[$category_id]) ? $this->datas[$category_id] : '';
  }
}
