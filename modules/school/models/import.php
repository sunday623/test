<?php
/**
 * @filesource modules/school/models/import.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Import;

use \Kotchasan\Http\Request;
use Kotchasan\Language;
use Gcms\Login;
use \Kotchasan\Text;

/**
 * เพิ่ม/แก้ไข ข้อมูลบุคลากร.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
  private $row = 0;
  private $params = array();
  private $login;

  /**
   * บันทึกข้อมูลที่ส่งมาจากฟอร์ม import.php
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, can_manage_student
    if ($request->initSession() && $request->isSafe() && $this->login = Login::isMember()) {
      if ($this->login['active'] == 1) {
        // ค่าที่ส่งมา
        $type = $request->post('type')->toString();
        // อัปโหลดไฟล์ csv
        foreach ($request->getUploadedFiles() as $item => $file) {
          /* @var $file \Kotchasan\Http\UploadedFile */
          if ($file->hasUploadFile()) {
            if (!$file->validFileExt(array('csv'))) {
              // ชนิดของไฟล์ไม่ถูกต้อง
              $ret['ret_'.$item] = Language::get('The type of file is invalid');
            } else {
              // import data from CSV
              if ($type == 'student' && Login::isTeacher('can_manage_student')) {
                // หมวดหมู่ของนักเรียน
                $this->categories = array();
                foreach (Language::get('SCHOOL_CATEGORY') as $key => $label) {
                  $this->categories[] = $key;
                  $this->params[$key] = $request->post($key)->toInt();
                }
                // import ข้อมูล
                \Kotchasan\Csv::read($file->getTempFileName(), array($this, 'importStudent'));
                // ส่งค่ากลับ
                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'school-students', 'id' => 0));
                $ret['alert'] = Language::replace('Successfully imported :count items', array(':count' => $this->row));
              } elseif ($type == 'grade' && Login::isTeacher('can_manage_student')) {
                // import ข้อมูล
                \Kotchasan\Csv::read($file->getTempFileName(), array($this, 'importGrade'));
                // ส่งค่ากลับ
                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'school-courses', 'id' => 0));
                $ret['alert'] = Language::replace('Successfully imported :count items', array(':count' => $this->row));
              } elseif ($type == 'course' && Login::isTeacher()) {
                // import ข้อมูล
                \Kotchasan\Csv::read($file->getTempFileName(), array($this, 'importCourse'));
                // ส่งค่ากลับ
                $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'school-courses', 'id' => 0));
                $ret['alert'] = Language::replace('Successfully imported :count items', array(':count' => $this->row));
              }
            }
          } elseif ($file->hasError()) {
            // upload Error
            $ret['ret_'.$item] = $file->getErrorMessage();
          } else {
            // ไม่ได้เลือกไฟล์
            $ret['ret_'.$item] = 'Please browse file';
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

  /**
   * ฟังก์ชั่นรับค่าจากการอ่าน CSV
   *
   * @param array $data
   */
  public function importCourse($data)
  {
    foreach ($data as $key => $value) {
      if ($key == 0) {
        // course_code
        $course['course_code'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 1) {
        // course_name
        $course['course_name'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 2) {
        // credit
        $course['credit'] = (double)$value;
      } elseif ($key == 3) {
        // period
        $course['period'] = (int)$value;
      } elseif ($key == 4) {
        // type
        $course['type'] = (int)$value;
      } elseif ($key == 5) {
        // class
        $course['class'] = (int)$value;
      } elseif ($key == 6) {
        // year
        $course['year'] = (int)$value;
      } elseif ($key == 7) {
        // term
        $course['term'] = (int)$value;
      } elseif ($key == 8) {
        // teacher_id
        $course['teacher_id'] = (int)$value;
      }
    }
    if ($course['course_code'] != '' && $course['course_name'] != '' && $course['credit'] > 0) {
      $where = array(
        array('course_code', $course['course_code'])
      );
      if ($course['teacher_id'] == 0) {
        $course['year'] = 0;
        $course['term'] = 0;
      } else {
        $where[] = array('teacher_id', $course['teacher_id']);
        $where[] = array('year', $course['year']);
        $where[] = array('term', $course['term']);
      }
      // ตรวจสอบข้อมูลซ้ำ
      $search = $this->db()->createQuery()
        ->select('id', 'teacher_id')
        ->from('course')
        ->where($where)
        ->toArray()
        ->first();
      if (!$search) {
        // บันทึกเกรด
        $this->db()->insert($this->getTableName('course'), $course);
        // นำเข้าข้อมูลสำเร็จ
        $this->row++;
      }
    }
  }

  /**
   * ฟังก์ชั่นรับค่าจากการอ่าน CSV
   *
   * @param array $data
   */
  public function importGrade($data)
  {
    $grade = array();
    $course = array();
    foreach ($data as $key => $value) {
      if ($key == 0) {
        // course_code
        $course['course_code'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 1) {
        // number
        $grade['number'] = (int)$value;
      } elseif ($key == 2) {
        // student_id
        $grade['student_id'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 3) {
        // grade
        $grade['grade'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 4) {
        // room
        $grade['room'] = (int)$value;
      } elseif ($key == 5) {
        // year
        $course['year'] = (int)$value;
      } elseif ($key == 6) {
        // term
        $course['term'] = (int)$value;
      }
    }
    // ตรวจสอบ id_card หรือ student_id ซ้ำ
    $q1 = $this->db()->createQuery()
      ->select('id')
      ->from('student')
      ->where(array('student_id', $grade['student_id']))
      ->limit(1);
    $q2 = $this->db()->createQuery()
      ->select('id')
      ->from('course')
      ->where(array(
        array('course_code', $course['course_code']),
        array('year', $course['year']),
        array('term', $course['term'])
      ))
      ->limit(1);
    $search = $this->db()->createQuery()
      ->from('course C')
      ->where(array('course_code', $course['course_code']))
      ->toArray()
      ->first('C.*', array($q1, 'student_id'), array($q2, 'course_id'));
    if ($search && $search['student_id']) {
      if (empty($search['course_id'])) {
        // ลงทะเบียนรายวิชาใหม่
        $save = $search;
        $save['year'] = $course['year'];
        $save['term'] = $course['term'];
        $save['teacher_id'] = $this->login['status'] == self::$cfg->teacher_status ? $this->login['id'] : 0;
        unset($save['id']);
        unset($save['course_id']);
        unset($save['student_id']);
        $grade['course_id'] = $this->db()->insert($this->getTableName('course'), $save);
      } else {
        // รายวิชาเดิม
        $grade['course_id'] = $search['course_id'];
      }
      $grade['student_id'] = $search['student_id'];
      // ตรวจสอบรายการซ้ำ
      $search = $this->db()->createQuery()
        ->from('grade')
        ->where(array(
          array('student_id', $grade['student_id']),
          array('course_id', $grade['course_id']),
          array('room', $grade['room'])
        ))
        ->first('id');
      if (!$search) {
        // บันทึกเกรด
        $this->db()->insert($this->getTableName('grade'), $grade);
      } else {
        // อัปเดทเกรด
        $this->db()->update($this->getTableName('grade'), $search->id, $grade);
      }
      // นำเข้าข้อมูลสำเร็จ
      $this->row++;
    }
  }

  /**
   * ฟังก์ชั่นรับค่าจากการอ่าน CSV
   *
   * @param array $data
   */
  public function importStudent($data)
  {
    $student = $this->params;
    $user = array();
    foreach ($data as $key => $value) {
      if ($key == 0) {
        // number
        $student['number'] = (int)$value;
      } elseif ($key == 1) {
        // student_id
        $student['student_id'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 2) {
        // name
        $user['name'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 3) {
        // id_card
        $student['id_card'] = preg_replace('/[^0-9]+/', '', $value);
      } elseif ($key == 4) {
        // birthday
        $year_offset = (int)Language::get('YEAR_OFFSET');
        if (preg_match('/([0-9]{4,4})[\-\/]([0-9]{1,2})[\-\/]([0-9]{1,2})/', $value, $match)) {
          $user['birthday'] = ((int)$match[1] - $year_offset).'-'.$match[2].'-'.$match[3];
          $password = $match[1].sprintf('%02d', $match[2]).sprintf('%02d', $match[3]);
        } elseif (preg_match('/([0-9]{1,2})[\-\/]([0-9]{1,2})[\-\/]([0-9]{4,4})/', $value, $match)) {
          $user['birthday'] = ((int)$match[3] - $year_offset).'-'.$match[2].'-'.$match[1];
          $password = $match[3].sprintf('%02d', $match[2]).sprintf('%02d', $match[1]);
        }
      } elseif ($key == 5) {
        // phone
        $user['phone'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 6) {
        // sex
        $user['sex'] = $value == 'f' || $value == 'm' ? $value : '';
      } elseif ($key == 7) {
        // address
        $student['address'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 8) {
        // parent
        $student['parent'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 9) {
        // address
        $student['parent_phone'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } else {
        // หมวดหมู่
        $student[$this->categories[$key - 10]] = (int)$value;
      }
    }
    if ($user['name'] != '') {
      if ($student['id_card'] != '' && isset($password)) {
        $user['username'] = $student['id_card'];
        $user['password'] = $password;
      }
      // ตรวจสอบ id_card หรือ student_id ซ้ำ
      $query = $this->db()->createQuery()
        ->from('student S')
        ->join('user U', 'INNER', array('U.id', 'S.id'))
        ->toArray();
      if ($student['id_card'] != '') {
        $query->where(array(
          array('S.id_card', $student['id_card']),
          array('S.student_id', $student['student_id'])
          ), 'OR');
      } else {
        $query->where(array('S.student_id', $student['student_id']));
      }
      $search = $query->first('S.id');
      if (!$search) {
        // สถานะครู
        $user['status'] = isset(self::$cfg->student_status) ? self::$cfg->student_status : 0;
        // register
        $user = \Index\Register\Model::execute($this, $user);
        // id ของ student
        $student['id'] = $user['id'];
        // บันทึก student
        $table_name = $this->getTableName('student');
        $this->db()->delete($table_name, array('id', $student['id']));
        $this->db()->insert($table_name, $student);
        // นำเข้าข้อมูลสำเร็จ
        $this->row++;
      }
    }
  }
}