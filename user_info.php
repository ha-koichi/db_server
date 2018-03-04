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

$department = $data["department"];
$current = $data["current"];

try
{
  $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);

  $stmt = $dbh -> prepare("select * from users left join activities on users.p_id = activities.p_id
                          where users.department = :department
                          and (activities.from_date_time <= :current and :current <= activities.to_date_time)");

  $stmt->bindParam(':department', $department, PDO::PARAM_STR);
  $stmt->bindParam(':current', $current, PDO::PARAM_STR);

  $stmt->execute();
  $count = $stmt -> rowCount();
  if ($count > 0) {
    while ($row = $stmt->fetchObject()){
      $activeData[]=array(
        'a_id'=> $row->a_id,
        'title'=> $row->activity_title,
        'description'=> $row->activity_description,
        'from_date'=> date('h:i A',strtotime($row->from_date_time)),
        'to_date'=> date('h:i A', strtotime($row->to_date_time)),
        'location'=> $row->location,
        'grace_as'=> $row->grace_as,
        'remarks'=> $row->remarks
      );
    }
  } else {
    $activeData = '';
  }

  $stmt = $dbh -> prepare("select * from users where users.department = :department");
  $stmt->bindParam(':department', $department, PDO::PARAM_STR);
  $stmt->execute();
  $row = $stmt->fetchObject();
  $count = $stmt -> rowCount();
  if ($count > 0) {
  $userData[]=array(
    'id'=> $row->p_id,
    'name'=> $row->name,
    '$picture'=> base64_encode($row->picture),
    'department'=> $row->department,
    'designation'=> $row->designation,
    'email'=> $row->email,
    'mobile'=> $row->mobile,
    'activities'=> $activeData
    );
  } else {
    $userData[]=array(
      'id'=> '',
      'name'=> '',
      '$picture'=> '',
      'department'=> '',
      'designation'=> '',
      'email'=> '',
      'mobile'=> '',
      'activities'=> ''
    );
  }
if (!empty($userData)){
  header('Content-type: application/json');
  echo json_encode($userData);
}

  exit;

}
catch (PDOException $e)
{

die('Error:' . $e->getMessage());
}

?>
