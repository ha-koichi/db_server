<?php
require_once('config.php');

if (
    !(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    && (!empty($_SERVER['SCRIPT_FILENAME']) && '' === basename($_SERVER['SCRIPT_FILENAME']))
    )
{
    die ('Dircectly unable to display this page.');
}

try
{
  $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);


  $stmt = $dbh -> prepare("update activities SET
                            activity_title = :title,
                            activity_description = :description,
                            from_date_time = :fromDataTime,
                            to_date_time = :toDataTime,
                            location = :location,
                            grace_as = :grace_as,
                            remarks = :remarks
                          WHERE a_id = :id");

  $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
  $stmt->bindParam(':title', $_POST['title'], PDO::PARAM_STR);
  $stmt->bindParam(':description', $_POST["description"], PDO::PARAM_STR);
  $stmt->bindParam(':fromDataTime', $_POST["fromDataTime"], PDO::PARAM_STR);
  $stmt->bindParam(':toDataTime', $_POST["toDataTime"], PDO::PARAM_STR);
  $stmt->bindParam(':location', $_POST["location"], PDO::PARAM_STR);
  $stmt->bindParam(':grace_as', $_POST["grace_as"], PDO::PARAM_STR);
  $stmt->bindParam(':remarks', $_POST["remarks"], PDO::PARAM_STR);

  $stmt->execute();
  exit;

}
catch (PDOException $e)
{

die('Error:' . $e->getMessage());
}

?>
