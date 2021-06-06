<?php
session_start();
// Connect to database
require('../../../Database/index.php');
// Get data
$id = isset($_GET["id"]) ? $_GET["id"] : '';
// data student
$name_lastname = $_SESSION["name"] . ' ' . $_SESSION["Lastname"];
// data Topic
$sql_topic = "SELECT * FROM Topic WHERE id_student = '" . $id . "'";
$result_topic = $conn->query($sql_topic);
$row_topic = $result_topic->fetch_assoc();
$name_teacher = $row_topic["Name_teacher"];

function format_date_event($group_date)
{
    $format_date = explode("-", $group_date);
    $years = number_format($format_date[2] + 543);
    $format_year = explode(",", $years);
    $year_s = $format_year[0] . $format_year[1];
    $m = $format_date[1];
    switch ($m) {
        case '01':
            $month = "มกราคม";
            break;
        case '02':
            $month = "กุมภาพันธ์";
            break;
        case '03':
            $month = "มีนาคม";
            break;
        case '04':
            $month = "เมษายน";
            break;
        case '05':
            $month = "พฤษภาคม";
            break;
        case '06':
            $month = "มิถุนายน";
            break;
        case '07':
            $month = "กรกฎาคม";
            break;
        case '08':
            $month = "สิงหาคม";
            break;
        case '09':
            $month = "กันยายน";
            break;
        case '10':
            $month = "ตุลาคม";
            break;
        case '11':
            $month = "พฤศจิกายน";
            break;
        case '12':
            $month = "ธันวาคม";
            break;
    }
    return $date_picker = 'วันที่ ' . $format_date[0] . ' ' . $month . ' ' .  $year_s;
}

require_once __DIR__ . '/../../../lib/pdf/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$style =
    '
<style>
.container{
    font-family: "Garuda";
}
.container .wrapper{
    font-size: 12pt;
    text-align: center;
}
h3{
  text-align: center;
  font-family: "Garuda";
  }
h4{
  font-family: "Garuda";
}
p{
  font-family: "Garuda";
}
/* วันที่ */
.date{
  position: relative;
  left: 60%;
}
#customers {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    font-family: "Garuda";
  }
  
  #customers td, #customers th {
    border: 1px solid #000;
    padding: 8px;
  }
  
  #customers tr:nth-child(even){background-color: #f2f2f2;}
  
  #customers tr:hover {background-color: #ddd;}
  
  #customers th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: center;
    color: #000;
  }

</style>';
$mpdf->WriteHTML($style);
$importance = str_replace("\n", "<br>\n", $row_topic["importance"]);
$objective = str_replace("\n", "<br>\n", $row_topic["objective"]);
$Principle = str_replace("\n", "<br>\n", $row_topic["Principle"]);
$plan_work = str_replace("\n", "<br>\n", $row_topic["plan_work"]);
$Plimit_work = str_replace("\n", "<br>\n", $row_topic["limit_work"]);
$vocabulary = str_replace("\n", "<br>\n", $row_topic["vocabulary"]);

$importance = str_replace(" ", "&nbsp;", $importance);
$objective = str_replace(" ", "&nbsp;", $objective);
$Principle = str_replace(" ", "&nbsp;", $Principle);
$plan_work = str_replace(" ", "&nbsp;", $plan_work);
$Plimit_work = str_replace(" ", "&nbsp;", $Plimit_work);
$vocabulary = str_replace(" ", "&nbsp;", $vocabulary);

$text = '
<div class = "container">
    <div class="wrapper">
        <p>แบบเสนอหัวข้องานวิจัยทางวิทยาการคอมพิวเตอร์</p>
        <p>คณะวิทยาศาสตร์มหาวิทยาลัยราชภัฏจันทรเกษม</p>
    </div>
    <div  class="detail">
        <p>
            1. รหัสประจำตัว ' . $id . ' ชื่อ-นามสกุล ' . $name_lastname . '<br />
            &nbsp;&nbsp;&nbsp;&nbsp;นักศึกษา ภาคในเวลาชั้นปี3 หมู่เรียน วท.บ.611(4)/1
        </p>
        <p>
            2. ชื่อหัวข้อที่นำเสนอ<br />
            &nbsp;&nbsp;&nbsp;&nbsp;ภาษาไทย : ' . $row_topic["NameProjectTH"] . '<br />
            &nbsp;&nbsp;&nbsp;&nbsp;ภาษาอังกฤษ : ' . $row_topic["NameProjectEng"] . '
        </p>
        <p>
            3. ความเป็นมาและความสำคัญของปัญหา <br />
            ' . $importance . '
        </p>
        <p>
            4.วัตถุประสงค์ <br />
            ' . $objective . '
        </p>
        <p>
            5. หลักการ ทฤษฎี เหตุผล <br />
            ' . $Principle . '
        </p>
        <p>
            6. ระยะเวลาดำเนินการ <br />
            <table id="customers" >
                <tr>
                    <th><h5>ลำดับ</h5></th>
                    <th><h5>เดือน</h5></th>
                    <th><h5>กิจกรรม</h5></th>
                </tr>
                ';
$mpdf->WriteHTML($text);
// data date activity
$sql_date = "SELECT * FROM date_event WHERE id_student = '" . $id . "'";
$result_date = $conn->query($sql_date);
$count = 1;
while ($row_date = $result_date->fetch_assoc()) {
    // format_date
    $date_picker = $row_date["Date"];
    $group_date = explode(" ถึง ", $date_picker);
    $date_1 = format_date_event($group_date[0]);
    $date_2 = format_date_event($group_date[1]);

    // show data
    $text_date_event = '
        <tr>
            <td style = "text-align:center">' . $count . '</td>
            <td style = "text-align:center">' . $date_1 . ' ถึง ' . $date_2 . '</td>
            <td>' . $row_date["Ativity"] . '</td>
        </tr>
        ';
    $mpdf->WriteHTML($text_date_event);
    $count++;
}
$text3 = '
            </table>
        </p>
        <p>
            7. แผนการดำเนินงาน ขอบเขตการศึกษา <br />
            ' . $plan_work  . '
        </p>
        <p>
            8. ประโยชน์ที่คาดว่าจะได้รับ <br />
            ' . $Plimit_work . '
        </p>
        <p>
            9. คำนิยามศัพท์เฉพาะ <br />
            ' . $vocabulary . '
        </p>
    </div>
</div>
';
$mpdf->WriteHTML($text3);

$text_page2 = "
<div class = 'container'>
    <div class = 'wrapper'>
        <p>
            10. ความคิดเห็นของอาจารย์ที่ปรึกษา <br />
            ...............................................................................................................................................
            ...............................................................................................................................................
            ...............................................................................................................................................
        </p>
        <br />
        <br />
        <br />
        <br />
        <br />
        <p>
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;ลงชื่อ.....................................................อาจารย์ที่ปรึกษา <br />
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;( " . $name_teacher . " )
        </p>
        <br />
        <br />
        <br />
        <p>
            11. ความเห็นของประธานหลักสูตร <br />
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<img src='https://img.icons8.com/ios/20/000000/unchecked-checkbox.png'/> อนุมัติ 
            &emsp;&emsp;&emsp;<img src='https://img.icons8.com/ios/20/000000/unchecked-checkbox.png'/> ไม่อนุมัติ
        </p>
        <br />
        <br />
        <br />
        <p>
            
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;ลงชื่อ.....................................................อาจารย์ที่ปรึกษา <br />
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;(ผู้ช่วยศาสตราจารย์ ดร. จันทรรัตน์ กิ่งแสง)
        </p>
    </div>
</div>
";
$mpdf->AddPage();
$mpdf->WriteHTML($text_page2);

$mpdf->Output();
$conn->close();
