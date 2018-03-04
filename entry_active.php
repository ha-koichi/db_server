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

  $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);
  $stmt = $dbh -> prepare("insert into activities (p_id, activity_title, activity_description, from_date_time, to_date_time, location, grace_as, remarks)
                          VALUES (:id, :title, :description, :fromDataTime, :toDataTime, :location, :grace_as, :remarks)");
  $stmt->bindParam(':id', $data["id"], PDO::PARAM_STR);
  $stmt->bindParam(':title', $data["title"], PDO::PARAM_STR);
  $stmt->bindParam(':description', $data["description"], PDO::PARAM_STR);
  $stmt->bindParam(':fromDataTime', $data["fromDataTime"], PDO::PARAM_STR);
  $stmt->bindParam(':toDataTime', $data["toDataTime"], PDO::PARAM_STR);
  $stmt->bindParam(':location', $data["location"], PDO::PARAM_STR);
  $stmt->bindParam(':grace_as', $data["grace_as"], PDO::PARAM_STR);
  $stmt->bindParam(':remarks', $data["remarks"], PDO::PARAM_STR);

  $stmt->execute();

  exit;

}
catch (PDOException $e)
{

die('Error:' . $e->getMessage());
}

?>
