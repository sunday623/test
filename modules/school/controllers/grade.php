<?php
/**
 * @filesource modules/school/controllers/grade.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Grade;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-grades
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * รายการนักเรียนที่ลงทะเบียนเรียน
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // อ่านข้อมูลนักเรียน
    $student = \School\User\Model::get($request->request('id')->toInt());
    if ($student && Login::isTeacher('can_mange_student')) {
      // ครู-อาจารย์, สามารถจัดการนักเรียนได้ ดูได้ทุกคน
    } elseif ($student && $login = Login::isStudent()) {
      if ($login['id'] != $student->id) {
        // นักเรียน ดูได้เฉพาะของตัวเอง
        $student = null;
      }
    }
    // ข้อความ title bar
    $this->title = Language::get('Grade Report');
    // เลือกเมนู
    $this->menu = 'module';
    // สมาชิก
    if ($student) {
      $this->title .= ' '.$student->name;
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-elearning">{LNG_School}</span></li>');
      $ul->appendChild('<li><span>'.$student->name.'</span></li>');
      $ul->appendChild('<li><span>{LNG_Grade}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-elearning">'.$this->title.'</h2>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('School\Grade\View')->render($request, $student));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}