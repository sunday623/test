<?php
/**
 * @filesource modules/school/controllers/course.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Course;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-course
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
    // อ่านข้อมูลตาม Request ที่ส่งมา
    $course = \School\Course\Model::getForWrite($request);
    // ข้อความ title bar
    $title = $course->id == 0 ? '{LNG_Add New}' : '{LNG_Edit}';
    $this->title = Language::trans($title.' {LNG_Course}');
    // เลือกเมนู
    $this->menu = 'module';
    // ครู-อาจาร์ย, สามารถจัดการรายวิชาได้
    if ($course && $login = Login::isTeacher('can_manage_course')) {
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_School}</span></li>');
      $ul->appendChild('<li><a href="{BACKURL?module=school-courses&id=0}">{LNG_Course}</a></li>');
      $ul->appendChild('<li><span>'.$title.'</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-write">'.$this->title.'</h2>'
      ));
      // แสดงฟอร์ม
      $section->appendChild(createClass('School\Course\View')->render($request, $course, $login));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}