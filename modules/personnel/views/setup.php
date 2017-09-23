<?php
/**
 * @filesource modules/personnel/views/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Setup;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Kotchasan\ArrayTool;

/**
 * module=personnel-setup
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
  /**
   * ข้อมูลโมดูล
   */
  private $position;
  private $department;
  private $personnel_status;

  /**
   * แสดงรายการบุคลากร สำหรับผู้ดูแล
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // สำหรับปุ่ม export
    $export = array();
    // สถานะบุคลากร
    $this->personnel_status = Language::get('PERSONNEL_STATUS');
    // เตรียมข้อมูลสำหรับใส่ลงในตาราง
    $fields = array('id', 'name', 'active');
    $headers = array(
      'name' => array(
        'text' => '{LNG_Name} {LNG_Surname}',
        'sort' => 'name'
      ),
      'active' => array(
        'text' => '{LNG_Status}',
        'class' => 'center',
        'sort' => 'active'
      )
    );
    $cols = array(
      'order' => array(
        'class' => 'center'
      ),
      'picture' => array(
        'class' => 'center'
      ),
      'active' => array(
        'class' => 'center'
      )
    );
    $filters = array();
    // หมวดหมู่ของบุคลากร
    foreach (Language::get('PERSONNEL_CATEGORY') as $key => $label) {
      $export[$key] = $request->request($key)->toInt();
      $this->$key = \Index\Category\Model::init($key);
      $fields[] = $key;
      $filters[$key] = array(
        'name' => $key,
        'text' => $label,
        'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), $this->$key->toSelect()),
        'default' => 0,
        'value' => $export[$key]
      );
      $headers[$key] = array(
        'text' => $label,
        'class' => 'center',
        'sort' => $key
      );
      $cols[$key] = array(
        'class' => 'center',
      );
    }
    $fields[] = 'order';
    $headers['order'] = array(
      'text' => '{LNG_Order}',
      'class' => 'center',
      'sort' => 'order'
    );
    $fields[] = 'picture';
    $headers['picture'] = array(
      'text' => '{LNG_Image}',
      'class' => 'center'
    );
    $export['active'] = $request->request('active', -1)->toInt();
    $filters['active'] = array(
      'name' => 'active',
      'text' => '{LNG_Status}',
      'options' => ArrayTool::merge(array(-1 => '{LNG_all items}'), $this->personnel_status),
      'default' => -1,
      'value' => $export['active']
    );
    // URL สำหรับส่งให้ตาราง
    $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
    // ตาราง
    $table = new DataTable(array(
      /* Uri */
      'uri' => $uri,
      /* Model */
      'model' => \Personnel\User\Model::toDataTable(),
      /* ฟิลด์ที่กำหนด (หากแตกต่างจาก Model) */
      'fields' => $fields,
      /* รายการต่อหน้า */
      'perPage' => $request->cookie('person_perPage', 30)->toInt(),
      /* เรียงลำดับ */
      'sort' => $request->cookie('person_sort', 'id DESC')->toString(),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/personnel/model/setup/action',
      'actionCallback' => 'dataTableActionCallback',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => '{LNG_With selected}',
          'options' => array(
            'delete' => '{LNG_Delete}'
          )
        ),
        array(
          'class' => 'button orange icon-excel',
          'id' => 'export&'.http_build_query($export),
          'text' => '{LNG_Download} {LNG_Personnel list}'
        ),
      ),
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('name'),
      /* ตัวเลือกการแสดงผลที่ส่วนหัว */
      'filters' => $filters,
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => $headers,
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => $cols,
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'view' => array(
          'class' => 'icon-info button brown notext',
          'id' => ':id',
          'title' => '{LNG_Details of} {LNG_Personnel}'
        ),
        'edit' => array(
          'class' => 'icon-edit button green notext',
          'href' => $uri->createBackUri(array('module' => 'personnel-write', 'id' => ':id')),
          'title' => '{LNG_Edit}'
        )
      ),
    ));
    // save cookie
    setcookie('person_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    // Javascript
    $table->script('initPerson("datatable");');
    // คืนค่า HTML
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
    $item['position'] = $this->position->get($item['position']);
    $item['department'] = $this->department->get($item['department']);
    $thumb = is_file(ROOT_PATH.$item['picture']) ? WEB_URL.$item['picture'] : WEB_URL.'modules/personnel/img/noimage.jpg';
    $item['picture'] = '<img src="'.$thumb.'" style="max-height:50px" alt=thumbnail>';
    $item['order'] = '<label><input type=text size=5 id=order_'.$item['id'].' value="'.$item['order'].'"></label>';
    if ($item['active'] == 0) {
      $item['active'] = '<a id=active_0_'.$item['id'].' class="icon-valid disabled" title="'.$this->personnel_status [0].'"></a>';
    } else {
      $item['active'] = '<a id=active_1_'.$item['id'].' class="icon-valid access" title="'.$this->personnel_status [1].'"></a>';
    }
    return $item;
  }
}