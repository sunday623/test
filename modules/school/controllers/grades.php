<?php
/**
 * @filesource modules/school/controllers/grades.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Grades;

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
    // อ่านข้อมูลรายวิชา
    $course = \School\Course\Model::find($request->request('subject')->toInt());
    // ข้อความ title bar
    $this->title = Language::get('Grade');
    // เลือกเมนู
    $this->menu = 'module';
    // ครู-อาจาร์ย, สามารถจัดการรายวิชาได้
    if (!empty($course->id) && Login::isTeacher('can_manage_course')) {
      $this->title .= ' {LNG_Course} '.$course->course_name.($course->course_code != '' ? ' ('.$course->course_code.')' : '');
      $this->title .= ' {LNG_Academic year} '.$course->year.'/'.$course->term;
      $this->title = Language::trans($this->title);
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-elearning">{LNG_School}</span></li>');
      $ul->appendChild('<li><a href="{BACKURL?module=school-courses&subject=0}">{LNG_Course}</a></li>');
      $ul->appendChild('<li><span>'.$course->course_name.'</span></li>');
      $ul->appendChild('<li><span>{LNG_Grade}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-elearning">'.$this->title.'</h2>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('School\Grades\View')->render($request, $course));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}