<?php
/**
 * @filesource modules/school/views/studentinfo.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Studentinfo;

use \Gcms\Login;
use \Index\Category\Model AS Category;
use \Kotchasan\Language;

/**
 * แสดงรายละเอียดนักเรียน (modal)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * แสดงฟอร์ม Modal สำหรับแสดงรายละเอียดบุคคลากร
   *
   * @param object $index
   * @param array $login
   * @return string
   */
  public function render($index, $login)
  {
    // picture
    if (!empty($index->picture) && is_file(ROOT_PATH.$index->picture)) {
      $img = WEB_URL.$index->picture;
    } else {
      $img = WEB_URL.'modules/school/img/noimage.jpg';
    }
    $content = array();
    $content[] = '<article class=personnel_view>';
    $content[] = '<header><h3 class=icon-info>{LNG_Details of} {LNG_Student}</h3></header>';
    $content[] = '<p><img src="'.$img.'" style="max-width:'.self::$cfg->student_w.'px;max-height:'.self::$cfg->student_h.'px"></p>';
    $content[] = '<div class=table>';
    $content[] = '<p class=tr><span class="td icon-customer">{LNG_Name} {LNG_Surname}</span><span class=td>:</span><span class=td>'.$index->name.'</span></p>';
    $content[] = '<p class=tr><span class="td icon-number">{LNG_Student ID}</span><span class=td>:</span><span class=td>'.$index->student_id.'</span></p>';
    foreach (Language::get('SCHOOL_CATEGORY') As $key => $value) {
      $content[] = '<p class=tr><span class="td icon-office">'.$value.'</span><span class=td>:</span><span class=td>'.Category::init($key)->get($index->$key).'</span></p>';
    }
    if (Login::checkPermission($login, 'can_config')) {
      $content[] = '<p class=tr><span class="td icon-profile">{LNG_Identification number}</span><span class=td>:</span><span class=td>'.$index->id_card.'</span></p>';
    }
    $content[] = '<p class=tr><span class="td icon-address">{LNG_Address}</span><span class=td>:</span><span class=td>'.$index->address.'</span></p>';
    $content[] = '<p class=tr><span class="td icon-phone">{LNG_Phone}</span><span class=td>:</span><span class=td>'.self::showPhone($index->phone).'</span></p>';
    $content[] = '<p class=tr><span class="td icon-customer">{LNG_Parent}</span><span class=td>:</span><span class=td>'.$index->parent.'</span></p>';
    $content[] = '<p class=tr><span class="td icon-phone">{LNG_Phone}</span><span class=td>:</span><span class=td>'.self::showPhone($index->parent_phone).'</span></p>';
    $content[] = '</div>';
    $content[] = '</article>';
    return implode('', $content);
  }
}