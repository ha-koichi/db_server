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


try
{
  $userData = null;

  $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);

  if (!empty($data["address"]) or !empty($data["department"]) or !empty($data["password"])) {
    if (empty($data["address"])){
      $data["address"] = '';
    }
    if (empty($data["department"])){
      $data["department"] = '';
    }
    if (empty($data["password"])){
      $data["password"] = '';
    }
    $stmt = $dbh -> prepare("select * from users where email = :address or department = :department or password = :password");
    $stmt->bindParam(':address', $data["address"], PDO::PARAM_STR);
    $stmt->bindParam(':department', $data["department"], PDO::PARAM_STR);
    $stmt->bindParam(':password', $data["password"], PDO::PARAM_STR);

    $stmt->execute();
    $count = $stmt -> rowCount();

    if ($count > 0) {
      while ($row = $stmt->fetchObject()){
        $userData[]=array(
          'id'=> $row->p_id,
          'name'=> $row->name,
          '$picture'=> base64_encode($row->picture),
          'department'=> $row->department,
          'designation'=> $row->designation,
          'email'=> $row->email,
          'mobile'=> $row->mobile,
          'password'=> $row->password,
          'adminFlg'=> $row->adminFlg,
        );
      }
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
