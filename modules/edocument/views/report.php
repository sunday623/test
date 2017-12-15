<?php
/**
 * @filesource modules/edocument/views/report.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Report;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\Date;

/**
 * module=edocument-report
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * รายงานการดาวน์โหลด
   *
   * @param Request $request
   * @param object $index
   * @return object
   */
  public function render(Request $request, $index)
  {
    // URL สำหรับส่งให้ตาราง
    $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
    // ตาราง
    $table = new DataTable(array(
      /* Uri */
      'uri' => $uri,
      /* Model */
      'model' => \Edocument\Report\Model::toDataTable($index->id),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id'),
      /* รายการต่อหน้า */
      'perPage' => $request->cookie('edocument_perPage', 30)->toInt(),
      /* เรียงลำดับ */
      'sort' => 'last_update DESC',
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'status' => array(
          'text' => '{LNG_Recipient}',
        ),
        'name' => array(
          'text' => '{LNG_Name} {LNG_Surname}'
        ),
        'last_update' => array(
          'text' => '{LNG_date}',
          'class' => 'center'
        ),
        'downloads' => array(
          'text' => '{LNG_Download}',
          'class' => 'center'
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'last_update' => array(
          'class' => 'center'
        ),
        'downloads' => array(
          'class' => 'center'
        ),
      )
    ));
    // save cookie
    setcookie('edocument_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    return $table->render();
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item, $o, $prop)
  {
    $item['last_update'] = $item['last_update'] == 0 ? '' : Date::format($item['last_update']);
    $item['status'] = isset(self::$cfg->member_status[$item['status']]) ? '<span class=status'.$item['status'].'>'.self::$cfg->member_status[$item['status']].'</span>' : '';
    return $item;
  }
}