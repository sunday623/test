<?php
/**
 * @filesource modules/edocument/controllers/report.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Report;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=edocument-sent
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * แสดงรายการเอกสาร
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ตรวจสอบรายการที่ต้องการ
    $index = \Edocument\Report\Model::get($request->request('id')->toInt());
    if ($index) {
      // ข้อความ title bar
      $this->title = Language::trans('{LNG_Download history} '.$index->topic);
      // เลือกเมนู
      $this->menu = 'module';
      // Login
      if ($login = Login::isMember()) {
        // แสดงผล
        $section = Html::create('section');
        // breadcrumbs
        $breadcrumbs = $section->add('div', array(
          'class' => 'breadcrumbs'
        ));
        $ul = $breadcrumbs->add('ul');
        $ul->appendChild('<li><span class="icon-edocument">{LNG_E-Document}</span></li>');
        $ul->appendChild('<li><a href="{BACKURL?module=edocument-sent&id=0}">{LNG_sent document}</a></li>');
        $ul->appendChild('<li><span>'.$index->topic.'</span></li>');
        $ul->appendChild('<li><span>{LNG_Download}</span></li>');
        $section->add('header', array(
          'innerHTML' => '<h2 class="icon-list">'.$this->title.'</h2>'
        ));
        // รายละเอียดการรับหนังสือ
        $section->appendChild(createClass('Edocument\Report\View')->render($request, $index));
        return $section->render();
      }
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}
