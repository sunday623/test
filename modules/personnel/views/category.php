<?php
/**
 * @filesource modules/personnel/views/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Category;

use \Kotchasan\DataTable;
use \Kotchasan\Html;
use \Kotchasan\Form;
use \Kotchasan\Language;

/**
 * module=personnel-category
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * รายการหมวดหมู่ที่ต้องการแก้ไข
   *
   * @param string $type
   * @return string
   */
  public function render($type)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/personnel/model/category/submit',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true,
        'token' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Details of} '.Language::find('PERSONNEL_CATEGORY', null, $type)
    ));
    // ตารางหมวดหมู่
    $table = new DataTable(array(
      /* ข้อมูลใส่ลงในตาราง */
      'datas' => \Index\Category\Model::toDataTable($type),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('id'),
      /* กำหนดให้ input ตัวแรก (id) รับค่าเป็นตัวเลขเท่านั้น */
      'onInitRow' => 'initFirstRowNumberOnly',
      'border' => true,
      'responsive' => true,
      'pmButton' => true,
      'showCaption' => false,
      'headers' => array(
        'category_id' => array(
          'text' => '{LNG_ID}'
        )
      )
    ));
    $fieldset->add('div', array(
      'class' => 'item',
      'innerHTML' => $table->render()
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button save large',
      'value' => '{LNG_Save}'
    ));
    // type
    $fieldset->add('hidden', array(
      'id' => 'type',
      'value' => $type
    ));
    // คืนค่าฟอร์ม
    return $form->render();
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item, $o, $prop)
  {
    $item['category_id'] = Form::text(array(
        'name' => 'category_id[]',
        'labelClass' => 'g-input icon-edit',
        'size' => 2,
        'value' => $item['category_id']
      ))->render();
    foreach (Language::installedLanguage() as $lng) {
      $item[$lng] = Form::text(array(
          'name' => $lng.'[]',
          'labelClass' => 'g-input',
          'value' => $item[$lng],
          'style' => 'background-image:url(../language/'.$lng.'.gif)'
        ))->render();
    }
    return $item;
  }
}
