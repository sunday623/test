<?php
/**
 * @filesource modules/school/views/grades.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Grades;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Kotchasan\ArrayTool;

/**
 * module=school-grades
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
  private $room;
  private $grade;

  /**
   * ตารางรายชื่อนักเรียน
   *
   * @param Request $request
   * @param object $course
   * @return string
   */
  public function render(Request $request, $course)
  {
    // ค่าที่ส่งมา
    $room = $request->request('room')->toInt();
    // โหลดตัวแปรต่างๆ
    $this->grade = '<option value=""></option>';
    foreach (Language::get('SCHOOL_GRADES') as $k => $v) {
      $this->grade .= '<option value="'.$k.'">'.$v.'</option>';
    }
    $rooms = \Index\Category\Model::init('room')->toSelect();
    $this->room = '<option value=""></option>';
    foreach ($rooms as $k => $v) {
      $this->room .= '<option value="'.$k.'">'.$v.'</option>';
    }
    // URL สำหรับส่งให้ตาราง
    $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
    // ตาราง
    $table = new DataTable(array(
      /* Uri */
      'uri' => $uri,
      /* Model */
      'model' => \School\Grades\Model::toDataTable($course->id),
      /* รายการต่อหน้า */
      'perPage' => $request->cookie('grades_perPage', 30)->toInt(),
      /* เรียงลำดับ */
      'sort' => 'room,number,student_id',
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id', 'student'),
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('student_id', 'name'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/school/model/grades/action',
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
          'class' => 'button orange icon-excel',
          'id' => 'export&subject='.$course->id.'&room='.$room,
          'text' => '{LNG_Download} {LNG_Grade}'
        ),
      ),
      /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
      'filters' => array(
        'room' => array(
          'name' => 'room',
          'text' => '{LNG_Room}',
          'default' => 0,
          'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), $rooms),
          'value' => $room
        )
      ),
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'number' => array(
          'text' => '{LNG_Number}',
        ),
        'student_id' => array(
          'text' => '{LNG_Student ID}',
        ),
        'name' => array(
          'text' => '{LNG_Name} {LNG_Surname}',
        ),
        'room' => array(
          'text' => '{LNG_Room}',
          'class' => 'center',
        ),
        'grade' => array(
          'text' => '{LNG_Grade}',
          'class' => 'center',
        ),
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'room' => array(
          'class' => 'center'
        ),
        'grade' => array(
          'class' => 'center'
        ),
      ),
    ));
    // save cookie
    setcookie('grades_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    // Javascript
    $table->script('initSchool("grades", "grade|number|room");');
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
    $item['name'] = '<a id=view_'.$item['student'].'>'.$item['name'].'</a>';
    $item['grade'] = '<label><select id=grade_'.$item['id'].'>'.str_replace('value="'.$item['grade'].'"', 'value="'.$item['grade'].'" selected', $this->grade).'</select></label>';
    $item['room'] = '<label><select id=room_'.$item['id'].'>'.str_replace('value="'.$item['room'].'"', 'value="'.$item['room'].'" selected', $this->room).'</select></label>';
    $item['number'] = '<label><input type=text size=5 id=number_'.$item['id'].' value="'.$item['number'].'"></label>';
    return $item;
  }
}