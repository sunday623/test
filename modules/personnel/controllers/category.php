<?php
/**
 * @filesource modules/personnel/controllers/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Category;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=personnel-category
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * หมวดหมู่ของระบบบุคคลากร
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ประเภทที่เลือก
    $type = $request->request('type', 'position')->topic();
    // ข้อความ title bar
    $title = Language::find('PERSONNEL_CATEGORY', null, $type);
    $this->title = Language::get('List of').' '.$title;
    // เลือกเมนู
    $this->menu = 'settings';
    // สามารถตั้งค่าระบบได้
    if ($title !== null && Login::checkPermission(Login::isMember(), 'can_config')) {
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-customer">{LNG_Settings}</span></li>');
      $ul->appendChild('<li><span>{LNG_Personnel}</span></li>');
      $ul->appendChild('<li><span>'.$title.'</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-category">'.$this->title.'</h2>'
      ));
      // แสดงฟอร์ม
      $section->appendChild(createClass('Personnel\Category\View')->render($type));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}
