<?php
/**
 * @filesource modules/school/models/courses.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Courses;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Language;

/**
 * ตารางรายวิชา
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * query รายวิชา สำหรับใส่ลงในตาราง (courses.php)
   *
   * @return \static
   */
  public static function toDataTable()
  {
    $model = new static;
    $q1 = $model->db()->createQuery()
      ->selectCount('id count')
      ->from('grade')
      ->where(array('course_id', 'C.id'));
    return $model->db()->createQuery()
        ->select('C.id', 'C.course_code', 'C.course_name', 'C.teacher_id', 'C.year', 'C.term', 'C.class', 'C.period', array($q1, 'student'))
        ->from('course C');
  }

  /**
   * รับค่าจาก action
   *
   * @param Request $request
   */
  public function action(Request $request)
  {
    $ret = array();
    // session, referer, admin
    if ($request->initSession() && $request->isReferer() && $login = Login::isTeacher('can_mange_course')) {
      if ($login['active'] == 1) {
        // รับค่าจากการ POST
        $action = $request->post('action')->toString();
        // id ที่ส่งมา
        if (preg_match_all('/,?([0-9]+),?/', $request->post('id')->toString(), $match)) {
          // Model
          $model = new \Kotchasan\Model;
          // ตาราง
          $table = $model->getTableName('course');
          if ($action === 'delete') {
            // ลบ
            $model->db()->delete($table, array('id', $match[1]), 0);
            // reload
            $ret['location'] = 'reload';
          }
        }
      }
    }
    if (empty($ret)) {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่า JSON
    echo json_encode($ret);
  }
}