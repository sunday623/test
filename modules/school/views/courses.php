<?php
/**
 * @filesource modules/school/views/courses.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Courses;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\ArrayTool;
use \Gcms\Login;

/**
 * module=school-courses
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
  /**
   * ข้อมูลโมดูล
   */
  private $teacher;
  private $class;

  /**
   * ตารางรายชื่อนักเรียน ที่ลงทะเบียนเรียนแล้ว และผลการเรียน
   *
   * @param Request $request
   * @param array $login
   * @return string
   */
  public function render(Request $request, $login)
  {
    // ค่าที่ส่งมา
    $class = $request->request('class')->toInt();
    $teacher = $request->request('teacher')->toInt();
    $year = $request->request('year', self::$cfg->academic_year)->toInt();
    $term = $request->request('term', self::$cfg->term)->toInt();
    // โหลดตัวแปรต่างๆ
    $this->teacher = \School\Teacher\Model::init();
    $this->class = \Index\Category\Model::init('class');
    if (Login::checkPermission($login, 'can_manage_course')) {
      // สามารถจัดการรายวิชาทั้งหมดได้
      $can_manage_course = 0;
      $teachers = ArrayTool::merge(array(0 => '{LNG_all items}'), $this->teacher->toSelect(0));
    } else {
      // ไม่สามารถจัดการรายวิชาทั้งหมดได้ แสดงเฉพาะรายการของตัวเอง
      $can_manage_course = $login['id'];
      $teacher = $login['id'];
      $teachers = $this->teacher->toSelect($can_manage_course);
    }
    $filters = array(
      'teacher_id' => array(
        'name' => 'teacher',
        'default' => 0,
        'text' => '{LNG_Teacher}',
        'options' => $teachers,
        'value' => $teacher
      ),
      'year' => array(
        'name' => 'year',
        'default' => 0,
        'text' => '{LNG_Academic year}',
        'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), \School\Academicyear\Model::toSelect()),
        'value' => $year
      ),
      'term' => array(
        'name' => 'term',
        'default' => 0,
        'text' => '{LNG_Term}',
        'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), \Index\Category\Model::init('term')->toSelect()),
        'value' => $term
      )
    );
    if ($can_manage_course == 0) {
      $filters['class'] = array(
        'name' => 'class',
        'default' => 0,
        'text' => '{LNG_Class}',
        'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), $this->class->toSelect()),
        'value' => $class
      );
      $hideColumns = array('id', 'term');
      $sort = 'year DESC,term DESC,teacher_id DESC';
    } else {
      $hideColumns = array('id', 'term', 'teacher_id');
      $sort = 'year DESC,term DESC';
    }
    // URL สำหรับส่งให้ตาราง
    $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
    // ตาราง
    $table = new DataTable(array(
      /* Uri */
      'uri' => $uri,
      /* Model */
      'model' => \School\Courses\Model::toDataTable(),
      /* รายการต่อหน้า */
      'perPage' => $request->cookie('courses_perPage', 30)->toInt(),
      /* เรียงลำดับ */
      'sort' => $request->cookie('courses_Sort', $sort)->topic(),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => $hideColumns,
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/school/model/courses/action',
      'actionCallback' => 'dataTableActionCallback',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => '{LNG_With selected}',
          'options' => array(
            'delete' => '{LNG_Delete}'
          )
        ),
        array(
          'class' => 'button green icon-plus',
          'href' => $uri->createBackUri(array('module' => 'school-course', 'class' => $class, 'teacher' => $teacher)),
          'text' => '{LNG_Add New} {LNG_Course}'
        )
      ),
      /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
      'filters' => $filters,
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'course_code' => array(
          'text' => '{LNG_Course Code}',
          'sort' => 'course_code'
        ),
        'course_name' => array(
          'text' => '{LNG_Course Name}',
          'sort' => 'course_name'
        ),
        'teacher_id' => array(
          'text' => '{LNG_Teacher}',
          'class' => 'center',
          'sort' => 'teacher_id'
        ),
        'year' => array(
          'text' => '{LNG_Academic year}',
          'class' => 'center',
          'sort' => 'year'
        ),
        'class' => array(
          'text' => '{LNG_Class}',
          'class' => 'center',
          'sort' => 'class'
        ),
        'period' => array(
          'text' => '{LNG_Period}',
          'class' => 'center',
        ),
        'student' => array(
          'text' => '{LNG_Student}',
          'class' => 'center',
        ),
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'teacher_id' => array(
          'class' => 'center'
        ),
        'year' => array(
          'class' => 'center',
        ),
        'class' => array(
          'class' => 'center'
        ),
        'period' => array(
          'class' => 'center'
        ),
        'student' => array(
          'class' => 'center'
        ),
      ),
      /* ฟังก์ชั่นตรวจสอบการแสดงปุ่มในแถว */
      'onCreateButton' => array($this, 'onCreateButton'),
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'grades' => array(
          'class' => 'icon-users button pink notext',
          'href' => $uri->createBackUri(array('module' => 'school-grades', 'subject' => ':id')),
          'title' => '{LNG_Student}'
        ),
        'register' => array(
          'class' => 'icon-register button orange notext',
          'href' => $uri->createBackUri(array('module' => 'school-register', 'subject' => ':id', 'class' => ':class', 'year' => $year, 'term' => $term)),
          'title' => '{LNG_Register course}'
        ),
        'edit' => array(
          'class' => 'icon-edit button green notext',
          'href' => $uri->createBackUri(array('module' => 'school-course', 'id' => ':id')),
          'title' => '{LNG_Edit}'
        ),
      ),
    ));
    // save cookie
    setcookie('courses_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    setcookie('courses_Sort', $table->sort, time() + 3600 * 24 * 365, '/');
    // คืนค่า HTML
    return $table->render();
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item, $o, $prop)
  {
    if ($item['teacher_id'] == 0) {
      $item['year'] = '';
    } else {
      $item['year'] = $item['year'].'/'.$item['term'];
    }
    $item['period'] = empty($item['period']) ? '' : $item['period'];
    $item['class'] = $this->class->get($item['class']);
    $item['teacher_id'] = $this->teacher->get($item['teacher_id']);
    return $item;
  }

  /**
   * ฟังก์ชั่นจัดการปุ่มในแต่ละแถว
   *
   * @param string $btn
   * @param array $attributes
   * @param array $items
   * @return array|boolean คืนค่า property ของปุ่ม ($attributes) ถ้าแสดงปุ่มได้, คืนค่า false ถ้าไม่สามารถแสดงปุ่มได้
   */
  public function onCreateButton($btn, $attributes, $items)
  {
    if ($btn == 'grades' || $btn == 'register') {
      return !empty($items['teacher_id']) || !empty($items['student']) ? $attributes : false;
    } else {
      return $attributes;
    }
  }
}
