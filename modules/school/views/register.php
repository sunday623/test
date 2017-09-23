<?php
/**
 * @filesource modules/school/views/register.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Register;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\ArrayTool;

/**
 * module=school-register
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
  private $class;
  private $room;
  private $uri;

  /**
   * ตารางรายชื่อนักเรียน สำหรับการลงทะเบียนเรียน
   *
   * @param Request $request
   * @param object $course
   * @return string
   */
  public function render(Request $request, $course)
  {
    // ค่าที่ส่งมา
    $class = $request->request('class')->toInt();
    $room = $request->request('room')->toInt();
    // โหลดตัวแปรต่างๆ
    $this->class = \Index\Category\Model::init('class');
    $this->room = \Index\Category\Model::init('room');
    // URL สำหรับส่งให้ตาราง กำหนดตัวแปรเพื่อให้สามารถส่งกลับมายังหน้าเดิมได้
    $params = array(
      '_module' => 'school-register',
      '_subject' => $course->id,
      '_class' => $class,
      '_room' => $room
    );
    $this->uri = $request->createUriWithGlobals(WEB_URL.'index.php')->withParams($params);
    // ตาราง
    $table = new DataTable(array(
      /* Uri */
      'uri' => $this->uri,
      /* Model */
      'model' => \School\Register\Model::toDataTable($course->id),
      /* รายการต่อหน้า */
      'perPage' => $request->cookie('register_perPage', 30)->toInt(),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id'),
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('name', 'student_id'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/school/model/register/action',
      'actionCallback' => 'dataTableActionCallback',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => '{LNG_With selected}',
          'options' => array(
            'register_'.$course->id => '{LNG_Register course}',
          )
        ),
      ),
      /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
      'filters' => array(
        'class' => array(
          'name' => 'class',
          'default' => 0,
          'text' => '{LNG_Class}',
          'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), $this->class->toSelect()),
          'value' => $class
        ),
        'room' => array(
          'name' => 'room',
          'default' => 0,
          'text' => '{LNG_Room}',
          'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), $this->room->toSelect()),
          'value' => $room
        ),
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
        'grade' => array(
          'text' => '{LNG_Register course}',
          'class' => 'center',
        ),
        'class' => array(
          'text' => '{LNG_Class}',
          'class' => 'center',
        ),
        'room' => array(
          'text' => '{LNG_Room}',
          'class' => 'center',
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'grade' => array(
          'class' => 'center'
        ),
        'class' => array(
          'class' => 'center'
        ),
        'room' => array(
          'class' => 'center'
        ),
      ),
    ));
    // save cookie
    setcookie('register_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
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
    $item['name'] = '<a id=view_'.$item['id'].'>'.$item['name'].'</a>';
    //$item['name'] = '<a href="'.$this->uri->withParams(array('module' => 'school-student', 'id' => $item['id'])).'" target=profile>'.$item['name'].'</a>';
    $item['grade'] = '<span class="icon-valid '.(empty($item['grade']) ? 'disabled' : 'access').'"></span>';
    $item['class'] = $this->class->get($item['class']);
    $item['room'] = $this->room->get($item['room']);
    return $item;
  }
}