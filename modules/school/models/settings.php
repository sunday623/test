<?php
/**
 * @filesource modules/school/models/settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Settings;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Language;
use \Gcms\Config;

/**
 * บันทึกการตั้งค่าโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{

  /**
   * รับค่าจาก settings.php
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, can_config
    if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
      if ($login['active'] == 1 && Login::checkPermission($login, 'can_config')) {
        // โหลด config
        $config = Config::load(ROOT_PATH.'settings/config.php');
        // รับค่าจากการ POST
        $config->school_name = $request->post('school_name')->topic();
        $config->phone = $request->post('phone')->topic();
        $config->fax = $request->post('fax')->topic();
        $config->address = $request->post('address')->topic();
        $config->provinceID = $request->post('provinceID')->number();
        $config->zipcode = $request->post('zipcode')->number();
        $config->student_w = max(100, $request->post('student_w')->toInt());
        $config->student_h = max(100, $request->post('student_h')->toInt());
        $config->teacher_status = $request->post('teacher_status')->toInt();
        $config->student_status = $request->post('student_status')->toInt();
        $config->academic_year = $request->post('academic_year')->toInt();
        $config->term = $request->post('term')->toInt();
        // save config
        if (Config::save($config, ROOT_PATH.'settings/config.php')) {
          // คืนค่า
          $ret['alert'] = Language::get('Saved successfully');
          $ret['location'] = 'reload';
          // เคลียร์
          $request->removeToken();
        } else {
          // ไม่สามารถบันทึก config ได้
          $ret['alert'] = sprintf(Language::get('File %s cannot be created or is read-only.'), 'settings/config.php');
        }
      }
    }
    if (empty($ret)) {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}
