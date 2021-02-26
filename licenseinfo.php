<?php
class LicenseInfo{
// dbection
private $db;
// Table
private $db_table = "license_info";
// Columns
public $mac;
public $license;
public $version;
public $productid;


// Db dbection
public function __construct($db){
$this->db = $db;
}

// GET ALL
public function getInfo(){
$sqlQuery = "SELECT * FROM ". $this->db_table. 
                        " WHERE MAC_ADDRESS = '" .$this->mac.
                       "' and LICENSE = '" . $this->license.
                      "' and VERSION = '" .$this->version.
                       "' and PRODUCT_ID = '" .$this->productid."'";
$this->result = $this->db->query($sqlQuery);
return $this->result;
}

public function UpdateInfo($timefrom,$timeto, $id){

$sqlQuery = "UPDATE ". $this->db_table ." SET ACTIVATION_TIME_FROM = ".$timefrom.",
ACTIVATION_TIME_TO = ".$timeto.",
UPDATE_TIME = current_timestamp(6),
STATUS = 1
WHERE id = ".$id;

$this->db->query($sqlQuery);
if($this->db->affected_rows > 0){
return true;
}
return false;
}

public function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

}

?>
