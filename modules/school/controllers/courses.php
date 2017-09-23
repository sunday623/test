<?php
/**
 * @filesource modules/school/controllers/courses.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Courses;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-courses
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * รายการรายวิชา
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ข้อความ title bar
    $this->title = Language::trans('{LNG_Manage} {LNG_Courses}');
    // เลือกเมนู
    $this->menu = 'module';
    // ครู-อาจาร์ย, สามารถจัดการรายวิชาได้
    if ($login = Login::isTeacher('can_manage_course')) {
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_School}</span></li>');
      $ul->appendChild('<li><span>{LNG_Course}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-elearning">'.$this->title.'</h2>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('School\Courses\View')->render($request, $login));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}