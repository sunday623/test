<?php
/**
 * @filesource modules/personnel/models/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Category;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Login;

/**
 * หมวดหมู่ของบุคคลากร
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * บันทึกหมวดหมู่
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, referer, can_config
    if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
      if ($login['active'] == 1 && Login::checkPermission($login, 'can_config')) {
        // ค่าที่ส่งมา
        $type = $request->post('type')->topic();
        $save = array();
        $category_exists = array();
        foreach ($request->post('category_id')->toInt() as $key => $value) {
          if (isset($category_exists[$value])) {
            $ret['ret_category_id_'.$key] = Language::replace('This :name already exist', array(':name' => 'ID'));
          } else {
            $category_exists[$value] = $value;
            $save[$key]['category_id'] = $value;
          }
        }
        foreach (Language::installedLanguage() as $lng) {
          foreach ($request->post($lng)->topic() as $key => $value) {
            if ($value != '') {
              $save[$key]['topic'][$lng] = $value;
            }
          }
        }
        if (empty($ret)) {
          // ชื่อตาราง
          $table_name = $this->getTableName('category');
          // db
          $db = $this->db();
          // ลบข้อมูลเดิม
          $db->delete($table_name, array('type', $type), 0);
          // เพิ่มข้อมูลใหม่
          foreach ($save as $item) {
            if (isset($item['topic'])) {
              $item['topic'] = serialize($item['topic']);
              $item['type'] = $type;
              $db->insert($table_name, $item);
            }
          }
          // คืนค่า
          $ret['alert'] = Language::get('Saved successfully');
          $ret['location'] = 'reload';
        }
      }
      // คืนค่า JSON
      echo json_encode($ret);
    }
  }
}