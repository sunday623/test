<?php
/**
 * @filesource modules/school/controllers/students.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Students;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-students
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * รายชื่อนักเรียน
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ข้อความ title bar
    $this->title = Language::get('Student list');
    // เลือกเมนู
    $this->menu = 'module';
    // ครู-อาจาร์ย, สามารถจัดการรายชื่อนักเรียนได้
    if (Login::isTeacher('can_manage_student')) {
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_School}</span></li>');
      $ul->appendChild('<li><span>{LNG_Student list}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-users">'.$this->title.'</h2>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('School\Students\View')->render($request));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}