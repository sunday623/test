<?php
/**
 * @filesource modules/school/models/grade.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Grade;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Language;

/**
 * รายงานผลการเรียนรายบุคคล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * query ผลการเรียน นักเรียนที่เลือก (grade.php)
   *
   * @param object $student
   * @return \static
   */
  public static function toDataTable($student)
  {
    $model = new static;
    return $model->db()->createQuery()
        ->select('G.id', 'C.course_code', 'C.course_name', 'C.type', 'C.credit', 'G.grade')
        ->from('grade G')
        ->join('course C', 'INNER', array('C.id', 'G.course_id'))
        ->where(array(
          array('G.student_id', $student->id),
          array('C.year', $student->year),
          array('C.term', $student->term),
    ));
  }

  /**
   * รับค่าจาก action
   *
   * @param Request $request
   */
  public function action(Request $request)
  {
    $ret = array();
    // session, referer, member
    if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
      if ($login['active'] == 1) {
        // รับค่าจากการ POST
        $action = $request->post('action')->toString();
        // id ที่ส่งมา
        if (preg_match_all('/,?([0-9]+),?/', $request->post('id')->toString(), $match)) {
          // Model
          $model = new \Kotchasan\Model;
          // ตาราง
          $table = $model->getTableName('grade');
          if ($action === 'delete') {
            // ลบ
            $model->db()->delete($table, array('id', $match[1]), 0);
            // reload
            $ret['location'] = 'reload';
          } elseif ($action === 'grade' || $action === 'number' || $action === 'room') {
            // อัปเดทข้อมูล
            $value = $request->post('value')->topic();
            $id = (int)$match[1][0];
            $model->db()->update($table, $id, array($action => $value));
            // คืนค่า
            $ret[$action.'_'.$id] = $value;
          } elseif ($action === 'view') {
            // ดูรายละเอียดนักเรียน
            $search = \School\User\Model::get((int)$match[1][0]);
            if ($search) {
              $ret['modal'] = Language::trans(createClass('School\Studentinfo\View')->render($search, $login));
            }
          }
        } elseif ($action == 'export') {
          // export เกรด
          $params = $request->getParsedBody();
          unset($params['action']);
          unset($params['src']);
          $params['module'] = 'school-download';
          $params['type'] = 'grade';
          $ret['location'] = WEB_URL.'export.php?'.http_build_query($params);
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