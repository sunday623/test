<?php
/**
 * @filesource modules/school/controllers/import.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Import;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-import
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * นำเข้ารายชื่อนักเรียน
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ข้อความ title bar
    $this->title = Language::get('Import').' ';
    switch ($request->request('type')->toString()) {
      case 'student':
        $className = 'School\Importstudent\View';
        $this->title .= Language::get('Student list');
        $breadcrumb = '<li><a href="{BACKURL?module=school-students&id=0}">{LNG_Student}</a></li>';
        // ครู-อาจาร์ย, สามารถจัดการรายชื่อนักเรียนได้
        $login = Login::isTeacher('can_manage_student');
        break;
      case 'grade':
        $className = 'School\Importgrade\View';
        $this->title .= Language::get('Grade');
        $breadcrumb = '<li><a href="{BACKURL?module=school-students&id=0}">{LNG_Student}</a></li>';
        // ครู-อาจาร์ย, สามารถจัดการรายชื่อนักเรียนได้
        $login = Login::isTeacher('can_manage_student');
        break;
    }
    // เลือกเมนู
    $this->menu = 'module';
    if (!empty($login)) {
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_School}</span></li>');
      $ul->appendChild($breadcrumb);
      $ul->appendChild('<li><span>{LNG_Import}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-import">'.$this->title.'</h2>'
      ));
      // แสดงฟอร์ม
      if (isset($className)) {
        $section->appendChild(createClass($className)->render($request, $login));
      }
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}
