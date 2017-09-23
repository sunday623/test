<?php
/**
 * @filesource modules/personnel/views/import.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Import;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\Http\UploadedFile;

/**
 * ฟอร์มนำเข้าข้อมูล บุคลากร
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * module=personnel-import
   *
   * @return string
   */
  public function render()
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/personnel/model/import/submit',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true,
        'token' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Import} {LNG_Personnel list}'
    ));
    // import
    $fieldset->add('file', array(
      'id' => 'import',
      'labelClass' => 'g-input icon-excel',
      'itemClass' => 'item',
      'label' => '{LNG_Browse file}',
      'comment' => Language::replace('File size is less than :size', array(':size' => UploadedFile::getUploadSize())),
      'accept' => array('csv')
    ));
    // หมวดหมู่ของบุคลากร
    $categories = array();
    foreach (Language::get('PERSONNEL_CATEGORY') as $key => $label) {
      $categories[] = '<a href="'.WEB_URL.'index.php?module=personnel-category&amp;type='.$key.'" target=_blank>'.$label.'</a>';
    }
    $file = 'modules/personnel/views/import_'.Language::name().'.html';
    if (!is_file(ROOT_PATH.$file)) {
      $file = 'modules/personnel/views/import_th.html';
    }
    $fieldset->add('aside', array(
      'class' => 'message',
      'innerHTML' => str_replace('{CATEGORIES}', implode(', ', $categories), file_get_contents(ROOT_PATH.$file))
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button save large',
      'value' => '{LNG_Import}'
    ));
    return $form->render();
  }
}