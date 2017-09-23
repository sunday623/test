<?php
/**
 * @filesource modules/school/models/course.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Course;

use \Kotchasan\Http\Request;
use Kotchasan\Language;
use Gcms\Login;

/**
 * เพิ่ม/แก้ไข คอร์ส
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
  /**
   * ตัวแปรเก็บข้อมูลที่ query
   *
   * @var array
   */
  private $datas = array();

  /**
   * อ่านรายวิชาจากฐานข้อมูล
   *
   * @param int $teacher_id มากกว่า 0 คืนค่ารายวิชาตามชั้นที่เลือก
   * @return \static
   */
  public static function init($teacher_id = 0)
  {
    $model = new static;
    $query = $model->db()->createQuery()
      ->select('course_name', 'course_code')
      ->from('course')
      ->groupBy('course_code')
      ->toArray()
      ->cacheOn();
    if ($teacher_id > 0) {
      $query->where(array('teacher_id', $teacher_id));
    }
    foreach ($query->execute() as $item) {
      $model->datas[$item['course_code']] = $item['course_name'].' ('.$item['course_code'].')';
    }
    return $model;
  }

  /**
   * คืนค่ารายชื่อรายวิชาใส่ลงใน select
   *
   * @return array
   */
  public function toSelect()
  {
    return $this->datas;
  }

  /**
   * อ่านข้อมูลรายการที่เลือก
   * ถ้าข้อมูลที่ส่งมา id = 0 หมายถึงรายการใหม่
   *
   * @param Request $request
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function getForWrite(Request $request)
  {
    $id = $request->request('id')->toInt();
    if (empty($id)) {
      return (object)array(
          'id' => 0,
          'course_code' => '',
          'course_name' => '',
          'period' => '',
          'credit' => '',
          'type' => 1,
          'teacher_id' => $request->request('teacher')->toInt(),
          'class' => $request->request('class')->toInt(),
          'year' => self::$cfg->academic_year,
          'term' => self::$cfg->term,
      );
    } else {
      // อ่านข้อมูลที่ $id
      return self::find($id);
    }
  }

  /**
   * อ่านข้อมูลที่ $id
   *
   * @param int $id
   * @return object|null คืนค่าข้อมูล object ไม่พบคืนค่า null
   */
  public static function find($id)
  {
    // query ข้อมูลที่ $id
    $model = new \Kotchasan\Model;
    return $model->db()->createQuery()
        ->from('course')
        ->where(array('id', $id))
        ->first();
  }

  /**
   * บันทึกข้อมูลที่ส่งมาจากฟอร์ม course.php
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, teacher
    if ($request->initSession() && $request->isSafe() && $login = Login::isTeacher('can_manage_course')) {
      if ($login['active'] == 1) {
        // ค่าที่ส่งมา
        $save = array(
          'course_name' => $request->post('course_name')->topic(),
          'course_code' => $request->post('course_code')->topic(),
          'teacher_id' => $request->post('teacher_id')->topic(),
          'class' => $request->post('class')->toInt(),
          'type' => $request->post('type')->toInt(),
          'period' => $request->post('period')->toInt(),
          'credit' => $request->post('credit')->toDouble(),
          'year' => $request->post('year')->toInt(),
          'term' => $request->post('term')->toInt(),
        );
        // ตรวจสอบรายการที่เลือก
        $index = self::getForWrite($request);
        if (!$index) {
          $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
        } else {
          // สามารถจัดการรายวิชาทั้งหมดได้
          if (!Login::checkPermission($login, 'can_manage_course')) {
            if ($index->id == 0) {
              $save['teacher_id'] = $login['id'];
            } else {
              unset($save['teacher_id']);
            }
          }
          // ใหม่และไม่ได้ระบุผู้สอนไม่ต้องมีปีการศึกษาและเทอม
          if ($index->id == 0 && empty($save['teacher_id'])) {
            $save['year'] = 0;
            $save['term'] = 0;
          }
          // course_name
          if ($save['course_name'] == '') {
            $ret['ret_course_name'] = 'Please fill in';
          }
          // course_code
          if ($save['course_code'] == '') {
            $ret['ret_course_code'] = 'Please fill in';
          }
          // ตรวจสอบข้อมูลซ้ำ
          $search = $this->db()->createQuery()
            ->select('id', 'teacher_id')
            ->from('course')
            ->where(array(
              array('course_code', $save['course_code']),
              array('year', $save['year']),
              array('term', $save['term']),
            ))
            ->toArray();
          $course_exists = false;
          foreach ($search->execute() As $item) {
            if (!empty($save['teacher_id']) && $item['teacher_id'] == $save['teacher_id'] && $item['id'] != $index->id) {
              $course_exists = true;
            } elseif (empty($save['teacher_id']) && empty($item['teacher_id']) && $item['id'] != $index->id) {
              $course_exists = true;
            }
          }
          if ($course_exists) {
            $ret['ret_course_code'] = Language::replace('This :name already exist', array(':name' => Language::get('Course Code')));
          }
          if (empty($ret)) {
            if ($index->id == 0) {
              // ใหม่
              $this->db()->insert($this->getTableName('course'), $save);
              // แสดงรายการใหม่
              $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'school-courses', 'id' => 0, 'page' => 1, 'sort' => 'id desc'));
            } else {
              // แก้ไข
              $this->db()->update($this->getTableName('course'), $index->id, $save);
              // กลับไปหน้าก่อนหน้า
              $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'school-courses', 'id' => 0));
            }
            // ส่งค่ากลับ
            $ret['alert'] = Language::get('Saved successfully');
          }
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