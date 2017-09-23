<?php
/**
 * @filesource modules/help/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Help\Index;

use \Kotchasan\Http\Request;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=help
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * ตั้งค่าระบบอีเมล์
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ข้อความ title bar
    $this->title = Language::get('Help');
    // เลือกเมนู
    $this->menu = 'help';
    // แสดงผล
    $section = Html::create('section');
    // breadcrumbs
    $breadcrumbs = $section->add('div', array(
      'class' => 'breadcrumbs'
    ));
    $ul = $breadcrumbs->add('ul');
    $ul->appendChild('<li><span class="icon-home">{LNG_Home}</span></li>');
    $ul->appendChild('<li><span>{LNG_Help}</span></li>');
    $section->add('header', array(
      'innerHTML' => '<h2 class="icon-help">'.$this->title.'</h2>'
    ));
    $section->appendChild(file_get_contents(ROOT_PATH.'modules/help/views/index.html'));
    return $section->render();
  }
}