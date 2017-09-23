<?php
/**
 * @filesource modules/school/models/students.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Students;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Language;

/**
 * โมเดลสำหรับแสดงรายการนักเรียน (students.php)
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
   * @return \static
   */
  public static function toDataTable()
  {
    $model = new static;
    return $model->db()->createQuery()
        ->select('S.*', 'U.name', 'U.phone', 'U.active')
        ->from('student S')
        ->join('user U', 'INNER', array('U.id', 'S.id'));
  }

  /**
   * ลิสต์รายการนักเรียนตามชั้นเรียนและห้องที่เลือก
   *
   * @param string $course_code
   * @param int $room
   * @return array คืนค่าลิสต์ของรหัสนักเรียน
   */
  public static function lists($course_code, $room)
  {
    $model = new static;
    $q1 = $model->db()->createQuery()
      ->select('class')
      ->from('course')
      ->where(array('course_code', $course_code));
    return $model->db()->createQuery()
        ->select('number', 'student_id')
        ->from('student')
        ->where(array(
          array('class', 'IN', $q1),
          array('room', $room),
          array('student_id', '!=', ''),
        ))
        ->order('number', 'student_id')
        ->toArray()
        ->cacheOn()
        ->execute();
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
    if ($request->initSession() && $request->isReferer() && $login = Login::isTeacher('can_manage_student')) {
      if ($login['active'] == 1) {
        // รับค่าจากการ POST
        $action = $request->post('action')->toString();
        if (preg_match_all('/,?([0-9]+),?/', $request->post('id')->toString(), $match)) {
          // Model
          $model = new \Kotchasan\Model;
          if ($action === 'delete') {
            // ลบ
            $ids = array();
            $query = $model->db()->createQuery()
              ->select('U.id', 'U.picture')
              ->from('user U')
              ->where(array('id', $match[1]))
              ->notExists('grade', array('student_id', 'U.id'))
              ->toArray();
            foreach ($query->execute() as $item) {
              if ($item['id'] != 1) {
                $ids[] = $item['id'];
                if (is_file(ROOT_PATH.$item['picture'])) {
                  // ลบไฟล์
                  unlink(ROOT_PATH.$item['picture']);
                }
              }
            }
            // ลบข้อมูล
            $model->db()->createQuery()->delete('student', array('id', $ids))->execute();
            $model->db()->createQuery()->delete('user', array('id', $ids))->execute();
            // reload
            $ret['location'] = 'reload';
          } elseif ($action === 'number') {
            // อัปเดทเลขที่
            $value = $request->post('value')->toInt();
            $id = (int)$match[1][0];
            $model->db()->update($model->getTableName('student'), $id, array('number' => $value));
            // คืนค่า
            $ret['number_'.$id] = $value;
          } elseif (preg_match('/^(room|class|department)_([0-9]+)$/', $action, $match2)) {
            // ห้อง, ชั้น, แผนก
            $model->db()->update($model->getTableName('student'), array(
              array('id', $match[1]),
              array('id', '!=', '1')
              ), array(
              $match2[1] => (int)$match2[2]
            ));
            // reload
            $ret['location'] = 'reload';
          } elseif ($action == 'graduate' || $action == 'studying') {
            // จบการศึกษา, กำลังเรียน
            $model->db()->update($model->getTableName('user'), array(
              array('id', $match[1]),
              array('status', self::$cfg->student_status)
              ), array(
              'active' => $action == 'studying' ? 1 : 0
            ));
            // reload
            $ret['location'] = 'reload';
          } elseif ($action === 'view') {
            // ดูรายละเอียดนักเรียน
            $search = \School\User\Model::get((int)$match[1][0]);
            if ($search) {
              $ret['modal'] = Language::trans(createClass('School\Studentinfo\View')->render($search, $login));
            }
          }
        } elseif ($action == 'export') {
          // export รายชื่อ
          $params = $request->getParsedBody();
          unset($params['action']);
          unset($params['src']);
          $params['module'] = 'school-download';
          $params['type'] = 'student';
          $ret['location'] = WEB_URL.'export.php?'.http_build_query($params);
        }
      }
    }
    if (empty($ret)) {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}