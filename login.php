<?php
require_once('config.php');

if (
  !(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
  && (!empty($_SERVER['SCRIPT_FILENAME']) && '' === basename($_SERVER['SCRIPT_FILENAME']))
  )
{
  die ('Dircectly unable to display this page.');
}

$getJson = file_get_contents('php://input');
$data = json_decode($getJson, true);

$set_ad = $data["address"];
$set_pw = $data["password"];

try
{
  $userData = null;

  $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);

  // Generating sql
  $sql = 'select * from users where email = \''.$set_ad.'\' && password = \''.$set_pw.'\'';
  $stmt = $dbh->query($sql);

  $count = $stmt -> rowCount();
  if ($count > 0) {
    // Generating user's JSON
    while ($row = $stmt->fetchObject()){
      $userData[]=array(
        'id'=> $row->p_id,
        'name'=> $row->name,
        'picture'=> base64_encode($row->picture),
        'department'=> $row->department,
        'designation'=> $row->designation,
        'email'=> $row->email,
        'mobile'=> $row->mobile,
        'adminFlg'=> $row->adminFlg,
      );
    }
  }
  header('Content-type: application/json');
  echo json_encode($userData);
  exit;
}
  catch (PDOException $e)
{
  die('Error:' . $e->getMessage());
}

?>
