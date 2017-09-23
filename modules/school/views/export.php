<?php
/**
 * @filesource modules/school/views/export.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Export;

use \Kotchasan\Template;
use \Kotchasan\Language;

/**
 * แสดงหน้าสำหรับพิมพ์
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * พิมพ์เกรด
   *
   * @param object $student
   * @param array $header
   * @param array $datas
   */
  public static function render($student, $header, $datas)
  {
    $thead = '';
    foreach ($header as $item) {
      $thead .= '<th>'.$item.'</th>';
    }
    $content = '';
    foreach ($datas As $items) {
      $content .= '<tr>';
      foreach ($items as $k => $item) {
        $class = $k == 1 ? '' : ' class=center';
        $content .= '<td'.$class.'>'.$item.'</td>';
      }
      $content .= '</tr>';
    }
    // template
    $template = Template::createFromFile(ROOT_PATH.'modules/school/views/mygrade.html');
    $template->add(array(
      '/%STUDENT%/' => $student->student_id,
      '/%NAME%/' => $student->name,
      '/%NUMBER%/' => $student->number,
      '/%DEPARTMENT%/' => $student->department,
      '/%CLASS%/' => \Index\Category\Model::init('class')->get($student->class),
      '/%ROOM%/' => \Index\Category\Model::init('room')->get($student->room),
      '/%YEAR%/' => $student->year,
      '/%TERM%/' => $student->term,
      '/%SCHOOLNAME%/' => self::$cfg->school_name,
      '/%SCHOOLPROVINCE%/' => \Kotchasan\Province::get(self::$cfg->provinceID),
      '/%THEAD%/' => $thead,
      '/%TBODY%/' => $content,
      '/{LANGUAGE}/' => Language::name(),
      '/{WEBURL}/' => WEB_URL
    ));
    echo Language::trans($template->render());
  }
}