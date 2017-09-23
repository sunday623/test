<?php
/**
 * @filesource modules/personnel/controllers/download.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Download;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Login;

/**
 * module=personnel-download
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * ส่งออกไฟล์ตัวอย่าง person.csv
   *
   * @param Request $request
   */
  public function execute(Request $request)
  {
    // ส่วนหัวของ CSV
    $header = array();
    $header['no'] = '#';
    $header['name'] = Language::trans('{LNG_Name} {LNG_Surname}');
    // ข้อมูลบุคลากร
    $person = array('no' => 0, 'name' => '');
    if (Login::checkPermission(Login::isMember(), 'can_manage_personnel')) {
      $header['id_card'] = Language::get('Identification number');
      $person['id_card'] = '';
    }
    $header['phone'] = Language::get('Phone');
    $person['phone'] = '';
    // หมวดหมู่ของบุคลากร
    $personnel_category = array();
    $params = array('active' => $request->get('active', -1)->toInt());
    foreach (Language::get('PERSONNEL_CATEGORY') as $key => $label) {
      $params[$key] = $request->get($key)->toInt();
      $header[$key] = $label;
      foreach (\Index\Category\Model::init($key)->toSelect() as $category_d => $item) {
        $personnel_category[$key][$category_d] = $item;
      }
      $person[$key] = '';
    }
    // custom item
    foreach (Language::find('PERSONNEL_DETAILS', array()) as $k => $v) {
      $header[$k] = $v;
      $person[$k] = '';
    }
    $n = 1;
    $datas = array();
    // query personnel
    foreach (\Personnel\Download\Model::getAll($params) as $item) {
      $person['no'] ++;
      $person['name'] = $item['name'];
      if (isset($person['id_card'])) {
        $person['id_card'] = $item['id_card'];
      }
      $person['phone'] = $item['phone'];
      foreach ($personnel_category as $key => $values) {
        $person[$key] = isset($values[$item[$key]]) ? $values[$item[$key]] : '';
      }
      $item['custom'] = @unserialize($item['custom']);
      if (is_array($item['custom'])) {
        foreach ($item['custom'] as $k => $v) {
          if (isset($header[$k])) {
            $person[$k] = $v;
          }
        }
      }
      $datas[] = $person;
    }
    // export
    \Kotchasan\Csv::send('person', $header, $datas);
  }
}