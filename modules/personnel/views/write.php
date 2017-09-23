<?php
/**
 * @filesource modules/personnel/views/write.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Write;

use \Kotchasan\Html;
use \Kotchasan\ArrayTool;
use \Kotchasan\Language;

/**
 * module=personnel-write
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * ฟอร์มสร้าง/แก้ไข บุคลากร
   *
   * @param object $index
   * @return string
   */
  public function render($index)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/personnel/model/write/submit',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true,
        'token' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Details of} {LNG_Personnel}'
    ));
    $groups = $fieldset->add('groups');
    // name
    $groups->add('text', array(
      'id' => 'personnel_name',
      'labelClass' => 'g-input icon-profile',
      'itemClass' => 'width50',
      'label' => '{LNG_Name} {LNG_Surname}',
      'maxlength' => 100,
      'value' => $index->name
    ));
    $groups = $fieldset->add('groups', array(
      'comment' => $index->id == 0 ? '{LNG_Identification number and Birthday Used to login}' : '<label><input type=checkbox name=updatepassword value=1>&nbsp;{LNG_Update username and password with personal identification number and birthday}</label>'
    ));
    // id_card
    $groups->add('text', array(
      'id' => 'personnel_id_card',
      'labelClass' => 'g-input icon-profile',
      'itemClass' => 'width50',
      'label' => '{LNG_Identification number}',
      'pattern' => '[0-9]+',
      'maxlength' => 13,
      'value' => $index->id_card,
      'validator' => array('keyup,change', 'checkIdcard')
    ));
    // birthday
    $groups->add('date', array(
      'id' => 'personnel_birthday',
      'labelClass' => 'g-input icon-calendar',
      'itemClass' => 'width50',
      'label' => '{LNG_Birthday}',
      'value' => $index->birthday,
    ));
    $groups = $fieldset->add('groups');
    // phone
    $groups->add('text', array(
      'id' => 'personnel_phone',
      'labelClass' => 'g-input icon-phone',
      'itemClass' => 'width50',
      'label' => '{LNG_Phone}',
      'maxlength' => 32,
      'value' => $index->phone,
    ));
    // order
    $groups->add('number', array(
      'id' => 'personnel_order',
      'labelClass' => 'g-input icon-sort',
      'itemClass' => 'width50',
      'label' => '{LNG_Order}',
      'placeholder' => '{LNG_Order of persons in positions}',
      'value' => $index->order,
    ));
    foreach (Language::get('PERSONNEL_CATEGORY') as $type => $label) {
      $fieldset->add('select', array(
        'id' => 'personnel_'.$type,
        'labelClass' => 'g-input icon-'.$type,
        'label' => $label,
        'itemClass' => 'item',
        'options' => ArrayTool::merge(array(0 => '{LNG_please select}'), \Index\Category\Model::init($type)->toSelect()),
        'value' => isset($index->$type) ? $index->$type : 0
      ));
    }
    // custom item
    foreach (Language::find('PERSONNEL_DETAILS', array()) as $type => $label) {
      $fieldset->add('text', array(
        'id' => 'personnel_'.$type,
        'labelClass' => 'g-input icon-'.$type,
        'itemClass' => 'item',
        'label' => $label,
        'value' => isset($index->custom[$type]) ? $index->custom[$type] : ''
      ));
    }
    // picture
    if (!empty($index->picture) && is_file(ROOT_PATH.$index->picture)) {
      $img = WEB_URL.$index->picture;
    } else {
      $img = WEB_URL.'modules/personnel/img/noimage.jpg';
    }
    $fieldset->add('file', array(
      'id' => 'personnel_picture',
      'labelClass' => 'g-input icon-upload',
      'itemClass' => 'item',
      'label' => '{LNG_Image}',
      'comment' => '{LNG_Browse image uploaded, type :type size :width*:height pixel (automatic resize)}',
      'dataPreview' => 'imgPicture',
      'previewSrc' => $img
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => '{LNG_Save}'
    ));
    // id
    $fieldset->add('hidden', array(
      'id' => 'personnel_id',
      'value' => $index->id
    ));
    \Gcms\Controller::$view->setContentsAfter(array(
      '/:type/' => 'jpg, jpeg, png',
      '/:width/' => self::$cfg->personnel_w,
      '/:height/' => self::$cfg->personnel_h,
    ));
    return $form->render();
  }
}
