<?php
/**
 * @filesource modules/school/controllers/student.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Student;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-student
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * แก้ไขข้อมูลนักเรียน
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    $params = array();
    foreach (Language::get('SCHOOL_CATEGORY') as $key => $label) {
      $params[$key] = $request->request($key)->toInt();
    }
    // อ่านข้อมูลที่เลือก
    $student = \School\User\Model::getForWrite($request->request('id')->toInt(), $params);
    // ข้อความ title bar
    $this->title = Language::get('Student');
    // เลือกเมนู
    $this->menu = 'module';
    // ครู-อาจารย์, สามารถจัดการนักเรียนได้
    $login = Login::isTeacher('can_manage_student');
    if (!$login) {
      // นักเรียน
      $login = Login::isStudent();
      $login = $login && $login['id'] == $student->id ? $login : false;
    }
    // สามารถจัดการได้
    if ($student && $login) {
      if ($login['id'] == $student->id) {
        // นักเรียน
        $title = Language::get('Profile');
      } else {
        // ครู-อาจารย์
        $title = Language::get($student->id == 0 ? 'Add New' : 'Edit');
      }
      $this->title = $title.' '.$this->title;
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_School}</span></li>');
      $ul->appendChild('<li><a href="{BACKURL?module=school-student&id=0}">{LNG_Student list}</a></li>');
      $ul->appendChild('<li><span>'.$title.'</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-profile">'.$this->title.'</h2>'
      ));
      // แสดงฟอร์ม
      $section->appendChild(createClass('School\Student\View')->render($request, $student, $login));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}
