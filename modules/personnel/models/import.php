<?php
/**
 * @filesource modules/personnel/models/import.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Personnel\Import;

use \Kotchasan\Http\Request;
use Kotchasan\Language;
use Gcms\Login;
use \Kotchasan\Text;

/**
 * เพิ่ม/แก้ไข ข้อมูลบุคลากร.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
  private $row = 0;
  private $categories;
  private $details;

  /**
   * บันทึกข้อมูลที่ส่งมาจากฟอร์ม import.php
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, can_config
    if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
      // สามารถจัดการรายชื่อบุคลากรได้
      if ($login['active'] == 1 && Login::checkPermission($login, 'can_manage_personnel')) {
        // อัปโหลดไฟล์ csv
        foreach ($request->getUploadedFiles() as $item => $file) {
          /* @var $file \Kotchasan\Http\UploadedFile */
          if ($file->hasUploadFile()) {
            if (!$file->validFileExt(array('csv'))) {
              // ชนิดของไฟล์ไม่ถูกต้อง
              $ret['ret_'.$item] = Language::get('The type of file is invalid');
            } else {
              // หมวดหมู่ของบุคลากร
              $this->categories = array_keys(Language::find('PERSONNEL_CATEGORY', array()));
              // รายละเอียดของบุคลาการ
              $this->details = array_keys(Language::find('PERSONNEL_DETAILS', array()));
              // import ข้อมูล
              \Kotchasan\Csv::read($file->getTempFileName(), array($this, 'onRow'));
            }
            // ส่งค่ากลับ
            $ret['alert'] = Language::replace('Successfully imported :count items', array(':count' => $this->row));
            $ret['location'] = WEB_URL.'index.php?module=personnel-setup';
          } elseif ($file->hasError()) {
            // upload Error
            $ret['ret_'.$item] = $file->getErrorMessage();
          } else {
            // ไม่ได้เลือกไฟล์
            $ret['ret_'.$item] = 'Please browse file';
          }
        }
      }
    }
    if (empty($ret)) {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }

  /**
   * ฟังก์ชั่นรับค่าจากการอ่าน CSV
   *
   * @param array $data
   */
  public function onRow($data)
  {
    $personnel = array();
    $user = array();
    $custom = array();
    // หมวดหมู่
    $n = 4 + sizeof($this->categories);
    foreach ($data as $key => $value) {
      if ($key == 0) {
        // name
        $user['name'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key == 1) {
        // id_card
        $personnel['id_card'] = preg_replace('/[^0-9]+/', '', $value);
      } elseif ($key == 2) {
        // birthday
        $year_offset = (int)Language::get('YEAR_OFFSET');
        if (preg_match('/([0-9]{4,4})[\-\/]([0-9]{1,2})[\-\/]([0-9]{1,2})/', $value, $match)) {
          $user['birthday'] = ((int)$match[1] - $year_offset).'-'.$match[2].'-'.$match[3];
          $password = $match[1].sprintf('%02d', $match[2]).sprintf('%02d', $match[3]);
        } elseif (preg_match('/([0-9]{1,2})[\-\/]([0-9]{1,2})[\-\/]([0-9]{4,4})/', $value, $match)) {
          $user['birthday'] = ((int)$match[3] - $year_offset).'-'.$match[2].'-'.$match[1];
          $password = $match[3].sprintf('%02d', $match[2]).sprintf('%02d', $match[1]);
        }
      } elseif ($key == 3) {
        // phone
        $user['phone'] = iconv('Windows-874', 'UTF-8', Text::topic($value));
      } elseif ($key < $n) {
        // หมวดหมู่
        $personnel[$this->categories[$key - 4]] = (int)$value;
      } else {
        // custom
        $value = Text::topic($value);
        if ($value != '') {
          $custom[] = iconv('Windows-874', 'UTF-8', $value);
        }
      }
    }
    if ($user['name'] != '') {
      $personnel['custom'] = empty($custom) ? '' : serialize($custom);
      if ($personnel['id_card'] != '' && isset($password)) {
        $user['username'] = $personnel['id_card'];
        $user['password'] = $password;
      }
      // ตรวจสอบ id_card หรือชื่อซ้ำ
      $query = $this->db()->createQuery()
        ->from('personnel P')
        ->join('user U', 'INNER', array('U.id', 'P.id'))
        ->toArray();
      if ($personnel['id_card'] != '') {
        $query->where(array('P.id_card', $personnel['id_card']));
      } else {
        $query->where(array('U.name', $user['name']));
      }
      $search = $query->first('P.id');
      if (!$search) {
        // สถานะครู
        $user['status'] = isset(self::$cfg->teacher_status) ? self::$cfg->teacher_status : 0;
        // register
        $user = \Index\Register\Model::execute($this, $user);
        // id ของ personnel
        $personnel['id'] = $user['id'];
        // บันทึก personnel
        $table_name = $this->getTableName('personnel');
        $this->db()->delete($table_name, array('id', $personnel['id']));
        $this->db()->insert($table_name, $personnel);
        // นำเข้าข้อมูลสำเร็จ
        $this->row++;
      }
    }
  }
}