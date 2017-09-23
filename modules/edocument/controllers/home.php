<?php
/**
 * @filesource modules/edocument/controllers/home.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Home;

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
    if ($login['status'] != self::$cfg->student_status) {
      \Index\Home\Controller::renderCard($card, 'icon-edocument', 'E-Document', \Edocument\Home\Model::getNew($login), 'New document', 'index.php?module=edocument-received');
    }
  }
}