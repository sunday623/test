<?php
/**
 * @filesource modules/school/controllers/export.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Export;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Login;

/**
 * module=school-export
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * ส่งออกไฟล์ csv หรือ การพิมพ์
   *
   * @param Request $request
   */
  public function execute(Request $request)
  {
    switch ($request->get('type')->toString()) {
      case 'student':
        $this->student($request);
        break;
      case 'grade':
        $this->grade($request);
        break;
      case 'mygrade':
        $this->mygrade($request);
        break;
    }
  }

  /**
   * ส่งออกเกรดของนักเรียนที่เลือก
   */
  private function mygrade(Request $request)
  {
    // อ่านข้อมูลนักเรียน
    $student = \School\User\Model::get($request->get('id')->toInt());
    if ($student && Login::isTeacher('can_mange_student')) {
      // ครู-อาจารย์, สามารถจัดการนักเรียนได้ ดูได้ทุกคน
    } elseif ($student && $login = Login::isStudent()) {
      if ($login['id'] != $student->id) {
        // นักเรียน ดูได้เฉพาะของตัวเอง
        $student = null;
      }
    }
    if ($student) {
      // ค่าที่ส่งมา
      $student->year = $request->get('year', self::$cfg->academic_year)->toInt();
      $student->term = $request->get('term', self::$cfg->term)->toInt();
      // header
      $header = array(
        Language::get('Course Code'),
        Language::get('Course Name'),
        Language::get('Type'),
        Language::get('Credit'),
        Language::get('Grade'),
      );
      // content
      $datas = array();
      $school_grades = Language::get('SCHOOL_GRADES');
      $course_typies = Language::get('COURSE_TYPIES');
      foreach (\School\Grade\Model::toDataTable($student)->toArray()->cacheOn()->execute() AS $item) {
        $datas[] = array(
          $item['course_code'],
          $item['course_name'],
          isset($course_typies[$item['type']]) ? $course_typies[$item['type']] : '',
          $item['credit'] == 0 ? '' : $item['credit'],
          isset($school_grades[$item['grade']]) ? $school_grades[$item['grade']] : '',
        );
      }
      if ($request->get('export')->toString() == 'print') {
        // ส่งออกเป็น HTML สำหรับพิมพ์
        \School\Export\View::render($student, $header, $datas);
      } else {
        $title = array(
          array(Language::trans('{LNG_Name} {LNG_Surname}'), $student->name),
          array(Language::get('Student ID'), $student->student_id),
          array(Language::get('Academic year'), $student->year.'/'.$student->term)
        );
        // ส่งออกไฟล์ csv
        \Kotchasan\Csv::send(implode('_', array(
          $student->student_id,
          $student->name,
          $student->year,
          $student->term,
          )), null, array_merge($title, array($header), $datas));
      }
    }
  }

  /**
   * ส่งออกข้อมูลตัวอย่าง grade เป็นไฟล์ CSV (grade.csv)
   */
  private function grade(Request $request)
  {
    // ค่าที่ส่งมา
    $course = $request->get('course')->topic();
    $room = $request->get('room')->toInt();
    $year = $request->get('year')->toInt();
    $term = $request->get('term')->toInt();
    // header
    $header = array(
      Language::get('Course'),
      Language::get('Number'),
      Language::get('Student ID'),
      Language::get('Grade'),
      Language::get('Room'),
      Language::get('Academic year'),
      Language::get('Term'),
    );
    $datas = array();
    foreach (\School\Students\Model::lists($course, $room) AS $item) {
      $datas[] = array($course, $item['number'], $item['student_id'], '', $room, $year, $term);
    }
    if (empty($datas)) {
      $datas = array(
        array($course, 1, 1000, 1, $room, $year, $term),
        array($course, 2, 1001, 4, $room, $year, $term)
      );
    }
    // ส่งออกไฟล์ grade.csv
    \Kotchasan\Csv::send('grade', $header, $datas);
  }

  /**
   * ส่งออกข้อมูลตัวอย่าง นักเรียน เป็นไฟล์ CSV (student.csv)
   */
  private function student(Request $request)
  {
    // header
    $header = array(
      Language::get('Number'),
      Language::trans('{LNG_Student ID} *, **'),
      Language::trans('{LNG_Name} {LNG_Surname} *'),
      Language::trans('{LNG_Identification number}, **'),
      Language::get('Birthday'),
      Language::get('Phone'),
      Language::get('Sex'),
      Language::get('Address'),
      Language::trans('{LNG_Name} {LNG_Surname} ({LNG_Parent})'),
      Language::trans('{LNG_Phone} ({LNG_Parent})'),
    );
    $birthday = ((int)date('Y') + (int)Language::get('YEAR_OFFSET')).'-01-31';
    $datas = array(
      array(1, '1000', 'นาย สมชาย มาดแมน', '', $birthday, '0123456789', 'f', '', '', ''),
      array(2, '1001', 'นางสาว สมหญิง สวยงาม', '', $birthday, '0123456788', 'm', '', '', '')
    );
    foreach (Language::get('SCHOOL_CATEGORY') as $k => $v) {
      $header[] = $v;
      $datas[0][] = $request->get($k)->toInt();
      $datas[1][] = $request->get($k)->toInt();
    }
    // ส่งออกไฟล์ student.csv
    \Kotchasan\Csv::send('student', $header, $datas);
  }
}
