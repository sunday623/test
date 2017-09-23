<?php
/**
 * @filesource modules/personnel/views/lists.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Lists;

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
  private $login;

  /**
   * ตารางบุคลากร สำหรับสมาชิกทั่วไป
   *
   * @param Request $request
   * @param array $login
   * @return string
   */
  public function render(Request $request, $login)
  {
    $this->login = $login;
    // เตรียมข้อมูลสำหรับใส่ลงในตาราง
    $filters = array();
    $fields = array(
      'id' => 'id',
      'name' => 'name',
    );
    $headers = array(
      'name' => array(
        'text' => '{LNG_Name} {LNG_Surname}',
      ),
    );
    $cols = array(
      'picture' => array(
        'class' => 'center'
      ),
    );
    // หมวดหมู่ของบุคลากร
    foreach (Language::get('PERSONNEL_CATEGORY') as $key => $label) {
      $this->$key = \Index\Category\Model::init($key);
      $fields[$key] = $label;
      $filters[$key] = array(
        'name' => $key,
        'text' => $label,
        'options' => ArrayTool::merge(array(0 => '{LNG_all items}'), $this->$key->toSelect()),
        'default' => 0,
        'value' => $request->request($key)->toInt()
      );
      $headers[$key] = array(
        'text' => $label,
        'class' => 'center',
      );
      $cols[$key] = array(
        'class' => 'center',
      );
    }
    $fields['picture'] = 'picture';
    $headers['picture'] = array(
      'text' => '{LNG_Image}',
      'class' => 'center'
    );
    // URL สำหรับส่งให้ตาราง
    $uri = $request->createUriWithGlobals(WEB_URL.'index.php');
    // ตาราง
    $table = new DataTable(array(
      /* Uri */
      'uri' => $uri,
      /* Model */
      'model' => \Personnel\User\Model::toDataTable(),
      /* query where */
      'defaultFilters' => array(
        array('active', 1)
      ),
      /* รายการต่อหน้า */
      'perPage' => $request->cookie('person_perPage', 30)->toInt(),
      /* เรียงลำดับ */
      'sort' => 'position,order',
      /* ฟิลด์ที่กำหนด (หากแตกต่างจาก Model) */
      'fields' => array_keys($fields),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id'),
      /* ไม่แสดง checkbox */
      'hideCheckbox' => true,
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('name'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/personnel/model/setup/action',
      'actionCallback' => 'dataTableActionCallback',
      'actions' => array(
        array(
          'class' => 'button pink icon-excel',
          'id' => 'export&active=1',
          'text' => '{LNG_Download} {LNG_Personnel list}'
        ),
      ),
      /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
      'filters' => $filters,
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => $headers,
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => $cols,
      /* ฟังก์ชั่นตรวจสอบการแสดงปุ่มในแถว */
      'onCreateButton' => array($this, 'onCreateButton'),
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'edit' => array(
          'class' => 'icon-edit button green notext',
          'href' => $uri->createBackUri(array('module' => 'personnel-write', 'id' => ':id')),
          'title' => '{LNG_Edit}'
        ),
        'view' => array(
          'class' => 'icon-info button brown notext',
          'id' => ':id',
          'title' => '{LNG_Details of} {LNG_Personnel}'
        ),
      ),
    ));
    // save cookie
    setcookie('person_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
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
    return $item;
  }

  /**
   * ฟังก์ชั่นจัดการปุ่มในแต่ละแถว
   *
   * @param string $btn
   * @param array $attributes
   * @param array $items
   * @return array|boolean คืนค่า property ของปุ่ม ($attributes) ถ้าแสดงปุ่มได้, คืนค่า false ถ้าไม่สามารถแสดงปุ่มได้
   */
  public function onCreateButton($btn, $attributes, $items)
  {
    if ($btn == 'view' || $items['id'] == $this->login['id']) {
      return $attributes;
    } else {
      return false;
    }
  }
}