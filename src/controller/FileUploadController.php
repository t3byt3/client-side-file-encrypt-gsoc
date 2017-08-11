<?php
namespace Drupal\client_side_file_crypto\Controller;

use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Access\AccessResult;

/**
 * Process file uploads.
 */
class FileUploadController {

  /**
   * Allow access for logged-in, authenitcated users.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($account->isAuthenticated());
  }

  /**
   * Process posted files.
   */
  public function create(Request $request) {
    if (strpos($request->headers->get('Content-Type'), 'multipart/form-data;') !== 0) {
      $res = new JsonResponse();
      $res->setStatusCode(400, 'must submit multipart/form-data');
      return $res;
    }

    $data = file_get_contents($_FILES['file']['tmp_name']);
    $mime = $_FILES['file']['type'];

    $file = file_save_data($data, "public://".rand(10,99).'_'.$_POST['csfcFileName'], FILE_EXISTS_REPLACE);
    $response['file_id'] = $file->id();
    $response['file_path'] = $file->url();

    return new JsonResponse($response);
  }


}