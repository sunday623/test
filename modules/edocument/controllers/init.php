<?php
/**
 * @filesource modules/edocument/controllers/init.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Init;

use \Kotchasan\Http\Request;
use \Gcms\Login;

/**
 * Init Module
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\KBase
{

  /**
   * ฟังก์ชั่นเริ่มต้นการทำงานของโมดูลที่ติดตั้ง
   * และจัดการเมนูของโมดูล
   *
   * @param Request $request
   * @param \Index\Menu\Controller $menu
   * @param array $login
   */
  public static function execute(Request $request, $menu, $login)
  {
    if ($login['status'] != self::$cfg->student_status) {
      $submenus = array(
        array(
          'text' => '{LNG_List of} {LNG_received document}',
          'url' => 'index.php?module=edocument-received'
        )
      );
      if (Login::checkPermission($login, 'can_upload_edocument')) {
        $submenus[] = array(
          'text' => '{LNG_List of} {LNG_sent document}',
          'url' => 'index.php?module=edocument-sent'
        );
      }
      $menu->add('module', '{LNG_E-Document}', null, $submenus);
      $submenus = array(
        array(
          'text' => '{LNG_Settings}',
          'url' => 'index.php?module=edocument-settings'
        )
      );
      $menu->add('settings', '{LNG_E-Document}', null, $submenus);
    }
  }

  /**
   * รายการ permission ของโมดูล
   *
   * @param array $permissions
   * @return array
   */
  public static function updatePermissions($permissions)
  {
    $permissions['can_handle_all_edocument'] = '{LNG_Can handle all documents}';
    $permissions['can_upload_edocument'] = '{LNG_Can upload your document file}';
    return $permissions;
  }
}