<?php
/**
 * @filesource modules/school/controllers/download.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Download;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;

/**
 * module=school-download
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * ส่งออกไฟล์ csv
   *
   * @param Request $request
   */
  public function execute(Request $request)
  {
    if ($request->isReferer()) {
      // ค่าที่ส่งมา
      $type = $request->get('type')->toString();
      if ($type == 'student') {
        $this->student($request);
      } elseif ($type == 'grade') {
        $this->grade($request);
      }
    } else {
      // 404
      header('HTTP/1.0 404 Not Found');
    }
    exit;
  }

  /**
   * ส่งออกรายชื่อนักเรียน
   *
   * @param Request $request
   */
  public function student(Request $request)
  {
    $header = array();
    $header[] = Language::get('Number');
    $header[] = Language::get('Student ID');
    $header[] = Language::trans('{LNG_Name} {LNG_Surname}');
    $header[] = Language::get('Identification number');
    $header[] = Language::get('Sex');
    $header[] = Language::get('Phone');
    $header[] = Language::get('Address');
    $header[] = Language::trans('{LNG_Name} {LNG_Surname} ({LNG_Parent})');
    $header[] = Language::trans('{LNG_Phone} ({LNG_Parent})');
    $params = array();
    $categories = array();
    foreach (Language::get('SCHOOL_CATEGORY') as $k => $v) {
      $params[$k] = $request->get($k)->toInt();
      $categories[$k] = \Index\Category\Model::init($k);
      $header[] = $v;
    }
    $sexes = Language::get('SEXES');
    $datas = array();
    foreach (\School\Download\Model::student($params, $request->get('active')->toInt()) As $item) {
      foreach ($params As $k => $v) {
        $item[$k] = $categories[$k]->get($item[$k]);
      }
      if (isset($sexes[$item['sex']])) {
        $item['sex'] = $sexes[$item['sex']];
      }
      $datas[] = $item;
    }
    \Kotchasan\Csv::send('student', $header, $datas);
  }

  /**
   * ส่งออกผลการเรียน
   *
   * @param Request $request
   */
  public function grade(Request $request)
  {
    $header = array();
    $header[] = Language::get('Number');
    $header[] = Language::get('Student ID');
    $header[] = Language::trans('{LNG_Name} {LNG_Surname}');
    $header[] = Language::get('Course Code');
    $header[] = Language::get('Academic year');
    $header[] = Language::get('Term');
    $header[] = Language::get('Class');
    $header[] = Language::get('Room');
    $header[] = Language::get('Grade');
    $params = array(
      'subject' => $request->get('subject')->toInt(),
      'room' => $request->get('room')->toInt()
    );
    $room = \Index\Category\Model::init('room');
    $class = \Index\Category\Model::init('class');
    $grade = Language::get('SCHOOL_GRADES');
    $datas = array();
    foreach (\School\Download\Model::grade($params) As $item) {
      $item['room'] = $room->get($item['room']);
      $item['class'] = $class->get($item['class']);
      $item['grade'] = isset($grade[$item['grade']]) ? $grade[$item['grade']] : '';
      $datas[] = $item;
    }
    \Kotchasan\Csv::send('grade', $header, $datas);
  }
}
