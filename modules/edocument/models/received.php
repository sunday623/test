<?php
/**
 * @filesource modules/edocument/models/received.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Received;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Language;

/**
 * โมเดลสำหรับแสดงรายการหนังสือรับ (received.php)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * Query ข้อมูลสำหรับส่งให้กับ DataTable
   * เฉพาะรายการที่มีสิทธิ์รับ
   *
   * @param array $login
   * @return /static
   */
  public static function toDataTable($login)
  {
    $model = new static;
    $sql2 = $model->db()->createQuery()
      ->select('E.downloads')
      ->from('edocument_download E')
      ->where(array(
        array('E.document_id', 'A.id'),
        array('E.member_id', (int)$login['id'])
      ))
      ->limit(1);
    return $model->db()->createQuery()
        ->select('A.id', 'A.document_no', array($sql2, 'new'), 'A.ext', 'A.topic', 'A.sender_id', 'A.size', 'A.last_update')
        ->from('edocument A')
        ->where(array('A.reciever', 'LIKE', '%,'.$login['status'].',%'))
        ->order('new ASC', 'A.last_update DESC');
  }

  /**
   * รับค่าจาก action
   *
   * @param Request $request
   */
  public function action(Request $request)
  {
    $ret = array();
    // session, referer, member
    if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
      if ($request->post('action')->toString() == 'detail') {
        // แสดงรายละเอียดของเอกสาร
        $document = \Edocument\View\Model::get($request->post('id')->toInt(), $login);
        if ($document) {
          $ret['modal'] = Language::trans(createClass('Edocument\View\View')->render($document, $login));
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
