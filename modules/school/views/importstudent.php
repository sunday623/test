<?php
/**
 * @filesource modules/school/views/importstudent.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Importstudent;

use \Kotchasan\Http\Request;
use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\Http\UploadedFile;

/**
 * module=school-import&type=student
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * ฟอร์มนำเข้าข้อมูลนักเรียน
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/school/model/import/submit',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true,
        'token' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Import} {LNG_Student list}'
    ));
    // หมวดหมู่ของนักเรียน
    $categories = array();
    foreach (Language::get('SCHOOL_CATEGORY') as $key => $label) {
      $fieldset->add('select', array(
        'id' => $key,
        'labelClass' => 'g-input icon-office',
        'itemClass' => 'item',
        'label' => $label,
        'options' => \Index\Category\Model::init($key)->toSelect(),
        'value' => $request->request($key)->toInt()
      ));
      $categories[] = '<a href="'.WEB_URL.'index.php?module=school-category&amp;type='.$key.'" target=_blank>'.$label.'</a>';
    }
    // import
    $fieldset->add('file', array(
      'id' => 'import',
      'labelClass' => 'g-input icon-excel',
      'itemClass' => 'item',
      'label' => '{LNG_Browse file}',
      'comment' => Language::replace('File size is less than :size', array(':size' => UploadedFile::getUploadSize())),
      'accept' => array('csv')
    ));
    $file = 'modules/school/views/importstudent_'.Language::name().'.html';
    if (!is_file(ROOT_PATH.$file)) {
      $file = 'modules/school/views/importstudent_th.html';
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
    // type
    $fieldset->add('hidden', array(
      'id' => 'type',
      'value' => 'student'
    ));
    // Javascript
    $form->script('initSchoolImportStudent();');
    return $form->render();
  }
}