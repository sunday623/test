<?php
/**
 * @filesource modules/personnel/views/personnelinfo.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Personnelinfo;

use \Kotchasan\Language;
use \Gcms\Login;

/**
 * แสดงรายละเอียดบุคคลากร (modal)
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
      $img = WEB_URL.'modules/personnel/img/noimage.jpg';
    }
    $content = array();
    $content[] = '<article class=personnel_view>';
    $content[] = '<header><h3 class=icon-info>{LNG_Details of} {LNG_Personnel}</h3></header>';
    $content[] = '<p><img src="'.$img.'" style="max-width:'.self::$cfg->personnel_w.'px;max-height:'.self::$cfg->personnel_h.'px"></p>';
    $content[] = '<div class=table>';
    $content[] = '<p class=tr><span class="td icon-customer">{LNG_Name} {LNG_Surname}</span><span class=td>:</span><span class=td>'.$index->name.'</span></p>';
    if (Login::checkPermission($login, 'can_manage_personnel')) {
      $content[] = '<p class=tr><span class="td icon-profile">{LNG_Identification number}</span><span class=td>:</span><span class=td>'.$index->id_card.'</span></p>';
    }
    $content[] = '<p class=tr><span class="td icon-phone">{LNG_Phone}</span><span class=td>:</span><span class=td>'.self::showPhone($index->phone).'</span></p>';
    foreach (Language::get('PERSONNEL_CATEGORY') as $type => $label) {
      $content[] = '<p class=tr><span class="td icon-'.$type.'">'.$label.'</span><span class=td>:</span><span class=td>'.\Index\Category\Model::init($type)->get($index->$type).'</span></p>';
    }
    $index->custom = @unserialize($index->custom);
    foreach (Language::find('PERSONNEL_DETAILS', array()) as $type => $label) {
      $value = is_array($index->custom) && isset($index->custom[$type]) ? $index->custom[$type] : '';
      $content[] = '<p class=tr><span class="td icon-'.$type.'">'.$label.'</span><span class=td>:</span><span class=td>'.$value.'</span></p>';
    }
    $content[] = '</div>';
    $content[] = '</article>';
    return implode('', $content);
  }
}