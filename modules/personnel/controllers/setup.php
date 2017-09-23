<?php
/**
 * @filesource modules/personnel/controllers/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Setup;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=personnel-setup
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * แสดงรายการบุคลากร
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    $active = $request->request('active', -1)->toInt();
    // ข้อความ title bar
    $this->title = Language::trans('{LNG_List of} {LNG_Personnel}');
    // เลือกเมนู
    $this->menu = 'module';
    // Login
    if ($login = Login::isMember()) {
      if (Login::checkPermission($login, 'can_manage_personnel')) {
        // ผู้ดูแล แสดงรายชื่อทั้งหมด
        $className = 'Personnel\Setup\View';
      } else {
        // สมาชิกทั่วไป แสดงรายชื่อบุคลากรปัจจุบัน
        $className = 'Personnel\Lists\View';
        $active = 1;
      }
      if ($active > -1) {
        $this->title = Language::get('List of').' '.Language::find('PERSONNEL_STATUS', null, $active);
      }
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-customer">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_Personnel}</span></li>');
      $ul->appendChild('<li><span>{LNG_Personnel list}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-list">'.$this->title.'</h2>'
      ));
      // ตาราง
      $section->appendChild(createClass($className)->render($request, $login));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}