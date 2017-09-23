<?php
/**
 * @filesource modules/school/views/student.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Student;

use \Kotchasan\Http\Request;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=school-student
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * ฟอร์ม เพิ่ม-แก้ไข ข้อมูลนักเรียน
   *
   * @param Request $request
   * @param array $student
   * @param array $login
   * @return string
   */
  public function render(Request $request, $student, $login)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/school/model/student/submit',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true,
        'token' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Details of} {LNG_Student}'
    ));
    $groups = $fieldset->add('groups');
    // name
    $groups->add('text', array(
      'id' => 'student_name',
      'labelClass' => 'g-input icon-customer',
      'itemClass' => 'width50',
      'label' => '{LNG_Name} {LNG_Surname}',
      'maxlength' => 100,
      'value' => $student->name
    ));
    // student_id
    $groups->add('text', array(
      'id' => 'student_student_id',
      'labelClass' => 'g-input icon-profile',
      'itemClass' => 'width50',
      'label' => '{LNG_Student ID}',
      'disabled' => $login['id'] == $student->id,
      'maxlength' => 13,
      'value' => $student->student_id
    ));
    $groups = $fieldset->add('groups', array(
      'comment' => $student->id == 0 ? '{LNG_Identification number and Birthday Used to login}' : '<label><input type=checkbox name=updatepassword value=1>&nbsp;{LNG_Update username and password with personal identification number and birthday}</label>'
    ));
    // id_card
    $groups->add('text', array(
      'id' => 'student_id_card',
      'labelClass' => 'g-input icon-profile',
      'itemClass' => 'width50',
      'label' => '{LNG_Identification number}',
      'pattern' => '[0-9]+',
      'maxlength' => 13,
      'value' => $student->id_card,
      'validator' => array('keyup,change', 'checkIdcard')
    ));
    // birthday
    $groups->add('date', array(
      'id' => 'student_birthday',
      'labelClass' => 'g-input icon-calendar',
      'itemClass' => 'width50',
      'label' => '{LNG_Birthday}',
      'value' => $student->birthday
    ));
    foreach (Language::get('SCHOOL_CATEGORY') as $key => $label) {
      $fieldset->add('select', array(
        'id' => 'student_'.$key,
        'labelClass' => 'g-input icon-office',
        'itemClass' => 'item',
        'label' => $label,
        'options' => \Kotchasan\ArrayTool::merge(array(0 => '{LNG_please select}'), \Index\Category\Model::init($key)->toSelect()),
        'disabled' => $login['id'] == $student->id,
        'value' => $student->$key
      ));
    }
    $groups = $fieldset->add('groups');
    // sex
    $groups->add('select', array(
      'id' => 'student_sex',
      'labelClass' => 'g-input icon-sex',
      'itemClass' => 'width50',
      'label' => '{LNG_Sex}',
      'options' => Language::get('SEXES'),
      'value' => $student->sex
    ));
    // phone
    $groups->add('text', array(
      'id' => 'student_phone',
      'labelClass' => 'g-input icon-phone',
      'itemClass' => 'width50',
      'label' => '{LNG_Phone}',
      'maxlength' => 32,
      'value' => $student->phone
    ));
    // address
    $fieldset->add('text', array(
      'id' => 'student_address',
      'labelClass' => 'g-input icon-address',
      'itemClass' => 'item',
      'label' => '{LNG_Address}',
      'maxlength' => 64,
      'value' => $student->address
    ));
    // picture
    if (!empty($student->picture) && is_file(ROOT_PATH.$student->picture)) {
      $img = WEB_URL.$student->picture;
    } else {
      $img = WEB_URL.'modules/school/img/noimage.jpg';
    }
    $fieldset->add('file', array(
      'id' => 'student_picture',
      'labelClass' => 'g-input icon-upload',
      'itemClass' => 'item',
      'label' => '{LNG_Image}',
      'comment' => '{LNG_Browse image uploaded, type :type size :width*:height pixel (automatic resize)}',
      'dataPreview' => 'imgPicture',
      'previewSrc' => $img
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Details of} {LNG_Parent}'
    ));
    $groups = $fieldset->add('groups');
    // parent
    $groups->add('text', array(
      'id' => 'student_parent',
      'labelClass' => 'g-input icon-customer',
      'itemClass' => 'width50',
      'label' => '{LNG_Name} {LNG_Surname}',
      'maxlength' => 100,
      'value' => $student->parent
    ));
    // parent_phone
    $groups->add('text', array(
      'id' => 'student_parent_phone',
      'labelClass' => 'g-input icon-phone',
      'itemClass' => 'width50',
      'label' => '{LNG_Phone}',
      'maxlength' => 32,
      'value' => $student->parent_phone
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button save large',
      'value' => '{LNG_Save}'
    ));
    // id
    $fieldset->add('hidden', array(
      'id' => 'student_id',
      'value' => $student->id
    ));
    \Gcms\Controller::$view->setContentsAfter(array(
      '/:type/' => 'jpg, jpeg, png',
      '/:width/' => self::$cfg->student_w,
      '/:height/' => self::$cfg->student_h,
    ));
    return $form->render();
  }
}