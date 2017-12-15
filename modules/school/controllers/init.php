<?php
/**
 * @filesource modules/school/controllers/init.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Init;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Login;

/**
 * Init Module
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\KBase
{

  /**
   * ฟังก์ชั่นเริ่มต้นการทำงานของโมดูลที่ติดตั้ง
   * และจัดการเมนูของโมดูล
   *
   * @param Request $request
   * @param \Index\Menu\Controller $menu
   * @param array $login
   */
  public static function execute(Request $request, $menu, $login)
  {
    if (Login::isTeacher('can_manage_student')) {
      // สามารถจัดการนักเรียนได้
      $submenus = array(
        array(
          'text' => '{LNG_Student list}',
          'url' => 'index.php?module=school-students'
        ),
        array(
          'text' => '{LNG_Import} {LNG_Student list}',
          'url' => 'index.php?module=school-import&amp;type=student'
        ),
        array(
          'text' => '{LNG_Add New} {LNG_Student}',
          'url' => 'index.php?module=school-student'
        ),
        array(
          'text' => '{LNG_Import} {LNG_Grade}',
          'url' => 'index.php?module=school-import&amp;type=grade'
        ),
      );
    } else {
      $submenus = array();
    }
    if (Login::isTeacher(array('can_manage_student', 'can_manage_course'))) {
      $submenus[] = array(
        'text' => '{LNG_Course}',
        'url' => 'index.php?module=school-courses'
      );
      $submenus[] = array(
        'text' => '{LNG_Import} {LNG_Course}',
        'url' => 'index.php?module=school-import&amp;type=course'
      );
      $submenus[] = array(
        'text' => '{LNG_Add New} {LNG_Course}',
        'url' => 'index.php?module=school-course'
      );
    }
    if ($login['status'] == self::$cfg->student_status) {
      $submenus[] = array(
        'text' => '{LNG_Grade Report}',
        'url' => 'index.php?module=school-grade&amp;id='.$login['id']
      );
    }
    $menu->add('module', '{LNG_School}', null, $submenus);
    $submenus = array(
      array(
        'text' => '{LNG_Settings}',
        'url' => 'index.php?module=school-settings'
      )
    );
    foreach (Language::get('SCHOOL_CATEGORY') as $type => $text) {
      $submenus[] = array(
        'text' => $text,
        'url' => 'index.php?module=school-category&amp;type='.$type
      );
    }
    $submenus[] = array(
      'text' => '{LNG_Term}',
      'url' => 'index.php?module=school-category&amp;type=term'
    );
    $menu->add('settings', '{LNG_School}', null, $submenus);
  }

  /**
   * รายการ permission ของโมดูล
   *
   * @param array $permissions
   * @return array
   */
  public static function updatePermissions($permissions)
  {
    $permissions['can_manage_student'] = '{LNG_Can manage students}';
    $permissions['can_manage_course'] = '{LNG_Can manage all courses}';
    return $permissions;
  }
}