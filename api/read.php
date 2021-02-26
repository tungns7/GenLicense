<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once '../database.php';
include_once '../licenseinfo.php';
$database = new Database();

$db = $database->getConnection();
$items = new LicenseInfo($db);
$records = $items->getInfo();
$itemCount = $records->num_rows;

if($itemCount > 0){
http_response_code(404);
$status = 0;
$EFFECT_TO = 0;
$NUMBER_WORKING_DAYS = 0;
$id = 0;
while ($row = $records->fetch_assoc())
{
$status = $row["STATUS"];
$EFFECT_TO = $row["EFFECT_TO"];
$NUMBER_WORKING_DAYS = $row["NUMBER_WORKING_DAYS"];
$id = $row["ID"];
}
if ($status == 0){
  $callApi = $items->CallAPI("GET","http://worldtimeapi.org/api/ip");
  $jsonObject = json_decode($callApi);
  $ActiveTime = $jsonObject->{'unixtime'};

if ($ActiveTime > $EFFECT_TO) {
  http_response_code(404);
  echo json_encode(
  array("Code" => "404","Mess" => "License hết hiệu lực.")
  );
} else {

  $ActiveTimeToDate = ((new DateTime())->setTimestamp($ActiveTime))->add(new DateInterval("P".$NUMBER_WORKING_DAYS."D"));
  $ActiveTimeUnix = strtotime( $ActiveTimeToDate->format('Y-m-d H:i:s'));
  $update = $items->UpdateInfo($ActiveTime,$ActiveTimeUnix,$id) ;

  if ($update) {
    http_response_code(200);
    echo json_encode(
    array("Code" => "200","Mess" => "Kích hoạt thành công.")
    );
  } else {
    http_response_code(404);
    echo json_encode(
    array("Code" => "404","Mess" => "Lỗi kích hoạt.")
    );
  }
}

} else {
  http_response_code(404);
  echo json_encode(
  array("Code" => "404","Mess" => "License đã được sử dụng.")
  );
}
}
else{
http_response_code(404);
echo json_encode(
array("Code" => "404","Mess" => "Không kích hoạt được sản phẩm.")
);
}
?>
