<?php
/**
 * @filesource modules/school/models/student.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace School\Student;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Login;
use \Kotchasan\File;

/**
 * เพิ่ม/แก้ไข ข้อมูลนักเรียน
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * บันทึกข้อมูลที่ส่งมาจากฟอร์ม student.php
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, สมาชิก
    if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
      // ตรวจสอบค่าที่ส่งมา
      $index = \School\User\Model::getForWrite($request->post('student_id')->toInt());
      if (!$index) {
        // ไม่พบรายการที่แก้ไข
        $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
      } else {
        // ครู-อาจารย์, สามารถจัดการนักเรียนได้
        if (!Login::isTeacher('can_manage_student')) {
          // ตัวเอง
          $login = $login['id'] == $index->id ? $login : false;
        }
        if ($login && $login['active'] == 1) {
          // ค่าที่ส่งมา
          $user = array(
            'name' => $request->post('student_name')->topic(),
            'phone' => $request->post('student_phone')->topic(),
            'birthday' => $request->post('student_birthday')->date(),
            'sex' => $request->post('student_sex')->topic(),
          );
          $student = array(
            'id_card' => $request->post('student_id_card')->number(),
            'student_id' => $request->post('student_student_id')->topic(),
            'address' => $request->post('student_address')->topic(),
            'parent' => $request->post('student_parent')->topic(),
            'parent_phone' => $request->post('student_parent_phone')->topic(),
          );
          $urls = array();
          if ($login['id'] != $index->id) {
            foreach (Language::get('SCHOOL_CATEGORY') as $key => $label) {
              $student[$key] = $request->post('student_'.$key)->toInt();
              $urls[$key] = $student[$key];
            }
          } else {
            // ตัวเอง ไม่สามารถอัปเดท student_id ได้
            unset($student['student_id']);
          }
          // อัปเดท Username และ Password ด้วย เลขประชาชนและวันเกิด
          $updatepassword = ($request->post('updatepassword')->toInt() == 1);
          if ($user['name'] == '') {
            // ไม่ได้กรอก name
            $ret['ret_student_name'] = 'Please fill in';
          } elseif ($updatepassword && $student['id_card'] == '') {
            // อัปเดท Username แต่ไม่ได้กรอก id_card
            $ret['ret_student_id_card'] = 'Please fill in';
          } elseif ($updatepassword && $user['birthday'] == '') {
            // อัปเดท Password แต่ไม่ได้กรอก วันเกิด
            $ret['ret_student_birthday'] = 'Please fill in';
          } elseif ($err = \School\User\Model::exists($index->id, $student)) {
            // เลขประชาชนหรือรหัสนักเรียนซ้ำ
            $ret['ret_student_'.$err] = Language::replace('This :name already exist', array(':name' => Language::get($err == 'student_id' ? 'Student ID' : 'Identification number')));
          } else {
            // ใหม่หรือต้องการปรับปรุง Username บันทึก user
            if ($student['id_card'] != '' && $user['birthday'] != '' && ($index->id == 0 || $updatepassword) && preg_match('/([0-9]{4,4})\-([0-9]{1,2})\-([0-9]{1,2})/', $user['birthday'], $match)) {
              $user['username'] = $student['id_card'];
              $user['password'] = ((int)Language::get('YEAR_OFFSET') + (int)$match[1]).sprintf('%02d', $match[2]).sprintf('%02d', $match[3]);
            }
            if ($index->id == 0) {
              // สถานะนักเรียน
              $user['status'] = isset(self::$cfg->student_status) ? self::$cfg->student_status : 0;
              // register
              $user = \Index\Register\Model::execute($this, $user);
              // id ของนักเรียน
              $student['id'] = $user['id'];
            } else {
              // id ของนักเรียนจาก DB
              $student['id'] = $index->id;
            }
            // อัปโหลดรูปภาพพร้อมปรับขนาด
            foreach ($request->getUploadedFiles() as $item => $file) {
              /* @var $file UploadedFile */
              if ($file->hasUploadFile()) {
                if (!File::makeDirectory(ROOT_PATH.DATA_FOLDER.'school/')) {
                  // ไดเรคทอรี่ไม่สามารถสร้างได้
                  $ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'school/');
                } elseif (!$file->validFileExt(array('jpg', 'jpeg', 'png'))) {
                  // ชนิดของไฟล์ไม่ถูกต้อง
                  $ret['ret_'.$item] = Language::get('The type of file is invalid');
                } elseif ($item == 'student_picture') {
                  $picture = DATA_FOLDER.'school/'.$student['id'].'.'.$file->getClientFileExt();
                  try {
                    $file->cropImage(array('jpg', 'jpeg', 'png'), ROOT_PATH.$picture, self::$cfg->student_w, self::$cfg->student_h);
                  } catch (\Exception $exc) {
                    // ไม่สามารถอัปโหลดได้
                    $ret['ret_'.$item] = Language::get($exc->getMessage());
                  }
                }
              } elseif ($file->hasError()) {
                // upload Error
                $ret['ret_'.$item] = $file->getErrorMessage();
              }
            }
            if (empty($ret)) {
              if ($index->id > 0) {
                // แก้ไข
                if (isset($picture)) {
                  $user['picture'] = $picture;
                }
                if ($updatepassword && isset($user['password']) && isset($user['username'])) {
                  $user['password'] = sha1($user['password'].$user['username']);
                }
                // update user
                $this->db()->update($this->getTableName('user'), $index->id, $user);
                // update student
                $this->db()->update($this->getTableName('student'), $index->id, $student);
              } else {
                // ใหม่
                if (isset($picture)) {
                  // update user picture
                  $this->db()->update($this->getTableName('user'), $student['id'], array('picture' => $picture));
                }
                // insert student
                $this->db()->insertOrUpdate($this->getTableName('student'), $student);
              }
              // ส่งค่ากลับ
              if ($login['id'] == $index->id) {
                // นักเรียน
                $ret['location'] = 'reload';
              } else {
                // ครู-อาจารย์
                if ($index->id == 0) {
                  // แสดงรายการใหม่
                  $urls['sort'] = 'number,id desc';
                  $urls['page'] = 1;
                } else {
                  $urls = array();
                }
                $urls['module'] = 'school-students';
                $urls['id'] = 0;
                $ret['location'] = $request->getUri()->postBack('index.php', $urls);
              }
              $ret['alert'] = Language::get('Saved successfully');
            }
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
}
