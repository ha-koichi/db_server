<?php
require_once('config.php');

if (
    !(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    && (!empty($_SERVER['SCRIPT_FILENAME']) && '' === basename($_SERVER['SCRIPT_FILENAME']))
    )
{
    die ('Dircectly unable to display this page.');
}

if (!empty($_FILES['file'])) {
  $img_path = $_FILES['file']["tmp_name"];
  $image = file_get_contents( $img_path );
}

try
{
  $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);

  if ($_POST["adminFlg"] == true){
    $_POST["adminFlg"] = 1;
  } else {
    $_POST["adminFlg"] = 0;
  }

  if (!empty($_FILES['file'])) {
    $stmt = $dbh -> prepare("update users SET
                              name = :name,
                              picture = :file,
                              designation = :designation,
                              department = :department,
                              email = :email,
                              mobile = :mobile,
                              password = :password,
                              adminFlg = :adminFlg
                            WHERE p_id = :id");

  $stmt->bindParam(':file', $image);
  } else {
    $stmt = $dbh -> prepare("update users SET
                              name = :name,
                              designation = :designation,
                              department = :department,
                              email = :email,
                              mobile = :mobile,
                              password = :password,
                              adminFlg = :adminFlg
                            WHERE p_id = :id");

  }

  $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
  $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
  $stmt->bindParam(':designation', $_POST["designation"], PDO::PARAM_STR);
  $stmt->bindParam(':department', $_POST["department"], PDO::PARAM_STR);
  $stmt->bindParam(':email', $_POST["email"], PDO::PARAM_STR);
  $stmt->bindParam(':mobile', $_POST["mobile"], PDO::PARAM_STR);
  $stmt->bindParam(':password', $_POST["password"], PDO::PARAM_STR);
  $stmt->bindValue(':adminFlg', $_POST["adminFlg"], PDO::PARAM_INT);

  $stmt->execute();
  exit;

}
catch (PDOException $e)
{

die('Error:' . $e->getMessage());
}

?>
