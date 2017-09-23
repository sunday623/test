<?php
/**
 * @filesource modules/school/controllers/home.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Home;

use \Kotchasan\Http\Request;

/**
 * Controller สำหรับการแสดงผลหน้า Home
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\KBase
{

  /**
   * ฟังก์ชั่นสร้าง card
   *
   * @param Request $request
   * @param \Kotchasan\Collection $card
   * @param array $login
   */
  public static function addCard(Request $request, $card, $login)
  {
    $datas = \School\Home\Model::getCount();
    \Index\Home\Controller::renderCard($card, 'icon-user', 'Student', $datas->student, 'Student list', 'index.php?module=school-students');
    \Index\Home\Controller::renderCard($card, 'icon-customer', 'Teacher', $datas->teacher, 'Personnel list', 'index.php?module=personnel-setup&amp;active=1');
  }
}