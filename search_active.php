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

  if (!empty($data["title"]) or !empty($data["description"]) or !empty($data["fromDataTime"]) or !empty($data["toDataTime"])) {

    // Generating sql
    if (empty($data["title"])){
      $data["title"] = '';
    }
    if (empty($data["description"])){
      $data["description"] = '';
    }
    if (empty($data["fromDataTime"])){
      $data["fromDataTime"] = '';
    }
    if (empty($data["toDataTime"])){
      $data["toDataTime"] = '';
    }

    // attaching like statement
    $title = '%'.$data["title"].'%';
    $description = '%'.$data["description"].'%';

    $stmt = $dbh -> prepare("select * from activities
                              where activity_title like :title
                              or activity_description like :description
                              or  (from_date_time <= :fromDataTime and to_date_time >= :toDataTime)");
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':fromDataTime', $data["fromDataTime"], PDO::PARAM_STR);
    $stmt->bindParam(':toDataTime', $data["toDataTime"], PDO::PARAM_STR);

  }

    $stmt->execute();
    $count = $stmt -> rowCount();

    if ($count > 0) {
      while ($row = $stmt->fetchObject()){
        $activeData[]=array(
          'a_id'=> $row->a_id,
          'activityTitle'=> $row->activity_title,
          'activityDescription'=> $row->activity_description,
          'fromDataTime'=> $row->from_date_time,
          'toDataTime'=> $row->to_date_time,
          'location'=> $row->location,
          'grace_as'=> $row->grace_as,
          'remarks'=> $row->remarks
        );
      }
    }

  header('Content-type: application/json');
  echo json_encode($activeData);
  exit;
}
  catch (PDOException $e)
{
  die('Error:' . $e->getMessage());
}

?>
