<!DOCTYPE html>
<html lang="jp" dir="ltr">
<body>
  <?php

        if(!is_dir("./upload")){
          mkdir("./upload");
        }
        require_once "Classes/PHPExcel.php";
        $objPHPExcel = new PHPExcel();
        require_once "Classes/PHPExcel/IOFactory.php";
        $filename = "WorkSystemForm.xlsx";

        /* $_POSTで値を持つ */
        $UserData = json_decode($_POST["Users"], true); //User Data
        $workdata = json_decode($_POST['Attendances_daily'] ,true); //User Dayly Data
        $monthlyWorkdata = json_decode($_POST['Attendances_monthly'] ,true);
        $YMs = $_POST['YMs']; //TimeSheet Selected Year and Month
        $SelectedYear = substr($YMs, 0, 4);
        $SelectedMonth = substr($YMs, 4, 6);

        $today = mktime(0,0,0,$SelectedMonth,1,$SelectedYear);

        $monthlyDay = date("t", $today); //一か月の最終日
        $strtotime = $SelectedYear."-".$SelectedMonth."-1"; //毎月1日の曜日を求めるための変数
        $dailyInt = date('w', strtotime($strtotime)); // date関数は0~6の数字をreturnする
        $daily = array('日','月','火','水','木','金','土');

        // 一人一か月のworkdata
        $dailyWork = array();
        foreach ($workdata as $key => $value) {
          for($i=1;$i<=$monthlyDay;$i++){
            if($i<10){
              if(empty($workdata[$key][$YMs."0".$i])){
                $dailyWork[$key][$YMs."0".$i]["start_time"] = "";
                $dailyWork[$key][$YMs."0".$i]["rest_time"] = "";
                $dailyWork[$key][$YMs."0".$i]["end_time"] = "";
                $dailyWork[$key][$YMs."0".$i]["workplace"] = "";
              }else{
                if(empty($workdata[$key][$YMs."0".$i]["start_time"])) $workdata[$key][$YMs."0".$i]["start_time"] = "";
                if(empty($workdata[$key][$YMs."0".$i]["rest_time"])) $workdata[$key][$YMs."0".$i]["rest_time"] = "";
                if(empty($workdata[$key][$YMs."0".$i]["end_time"])) $workdata[$key][$YMs."0".$i]["end_time"] = "";
                if(empty($workdata[$key][$YMs."0".$i]["workplace"])) $workdata[$key][$YMs."0".$i]["workplace"] = "";

                $dailyWork[$key][$YMs."0".$i]["start_time"] = $workdata[$key][$YMs."0".$i]["start_time"];
                $dailyWork[$key][$YMs."0".$i]["rest_time"] = $workdata[$key][$YMs."0".$i]["rest_time"];
                $dailyWork[$key][$YMs."0".$i]["end_time"] = $workdata[$key][$YMs."0".$i]["end_time"];
                $dailyWork[$key][$YMs."0".$i]["workplace"] = $workdata[$key][$YMs."0".$i]["workplace"];
      //    echo $workdata[$key][$YMs."0".$i]["workplace"];
              }

            }else{
              if(empty($workdata[$key][$YMs.$i])){
                $dailyWork[$key][$YMs.$i]["start_time"] = "";
                $dailyWork[$key][$YMs.$i]["rest_time"] = "";
                $dailyWork[$key][$YMs.$i]["end_time"] = "";
                $dailyWork[$key][$YMs.$i]["workplace"] = "";
              }else{
                if(empty($workdata[$key][$YMs.$i]["start_time"])) $workdata[$key][$YMs.$i]["start_time"] = "";
                if(empty($workdata[$key][$YMs.$i]["rest_time"])) $workdata[$key][$YMs.$i]["rest_time"] = "";
                if(empty($workdata[$key][$YMs.$i]["end_time"])) $workdata[$key][$YMs.$i]["end_time"] = "";
                if(empty($workdata[$key][$YMs.$i]["workplace"])) $workdata[$key][$YMs.$i]["workplace"] = "";

                $dailyWork[$key][$YMs.$i]["start_time"] = $workdata[$key][$YMs.$i]["start_time"];
                $dailyWork[$key][$YMs.$i]["rest_time"] = $workdata[$key][$YMs.$i]["rest_time"];
                $dailyWork[$key][$YMs.$i]["end_time"] = $workdata[$key][$YMs.$i]["end_time"];
                $dailyWork[$key][$YMs.$i]["workplace"] = $workdata[$key][$YMs.$i]["workplace"];
            }
            }
          }
        }


        try {
          foreach ($UserData as $key => $value) { // $key == 一人ずつのuid

             $objReader=PHPExcel_IOFactory::createReaderForFile($filename);
             //$objReader->setReadDataOnly(true); // Read only
             $objPHPExcel = $objReader->load($filename);
             $objPHPExcel->setActiveSheetIndex(0);
             $sheet = $objPHPExcel->getActiveSheet();

             /* User Data Input */
             $sheet -> setCellValue('C3', PHPExcel_Shared_Date::FormattedPHPToExcel($SelectedYear, $SelectedMonth, 1));
             $sheet -> setCellValue('C4', $value['department']);
             $sheet -> setCellValue('C5', $value['name_kana']);
             $sheet -> setCellValue('C6', $value['user_name']);
             $sheet -> setCellValue('C7', $value['user_id']);
             $sheet -> setCellValue('C8', $value['division']);
             $sheet -> setCellValue('C9', $value['workplace']);
             $sheet -> setCellValue('C10', $value['workplace']);
          /* User Daily Data Input */
          foreach ($dailyWork as $key2 => $value2) { // key2==uid, value2==uidのworkdata
            if($key==$key2){
              $cellint = 13;
              $dailyInt = date('w', strtotime($strtotime)); // date関数は0~6の数字をreturnする
              foreach ($value2 as $key4 => $value4) {
                 $sheet -> setCellValue('A13', "1");
                 $sheet -> setCellValue('B'.$cellint, $daily[$dailyInt]); // day of the week
                 if(empty($value4['start_time'])){
                  $sheet -> setCellValue('C'.$cellint, "");
                 }else{
                   $sheet -> setCellValue('C'.$cellint, $value4['workplace']); // workplace
                 }
                 $sheet -> setCellValue('E'.$cellint, CorrectTime($value4['start_time'], $SelectedYear, $SelectedMonth)); // starttime
                 $sheet -> setCellValue('F'.$cellint, CorrectTime($value4['end_time'], $SelectedYear, $SelectedMonth)); // endtime
                 $sheet -> setCellValue('G'.$cellint, $value4['rest_time']); // resttime
                 $sheet -> getStyle('F'.$cellint) -> getNumberFormat() -> setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);// View Style

                 $cellint++;

                 // 曜日を回すため
                 if($dailyInt<6){
                   $dailyInt++;
                 }else{
                   $dailyInt=0;
                 }
               }
            }
            if($monthlyDay<31){
              $sheet -> setCellValue('A43', ' ');
              if($monthlyDay<30){
                $sheet -> setCellValue('A42', ' ');
                if($monthlyDay<29){
                  $sheet -> setCellValue('A41', ' ');
                }
              }
            }
          }

          foreach ($monthlyWorkdata as $key3 => $value3) {
            if($key==$key3){
              foreach ($value3 as $key5 => $value5) {
                if($key5==$YMs){
                  if(empty($value5['attendances_memo'])) $value5['attendances_memo']="";
                  $sheet -> setCellValue('A46', $value5['attendances_memo']); //note
                }
              }
            }

          }
          $sheet -> setTitle($YMs); //sheet name
          $excelName = mb_convert_encoding($value['user_name'], 'SJIS-win', 'UTF-8');

          /* Export Excel File */
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

          print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
          header('Cache-Control: max-age=0');
          $objWriter->save("upload/".$YMs."_".$excelName.".xlsx");

        }

      }
      catch (exception $e) {
        echo 'エクセいるファイルの読み書き途中エラーが発生しました。';
      }

      function CorrectTime($TV, $SY, $SM) {
        if($TV) {
          $SelectedDate = PHPExcel_Shared_Date::FormattedPHPToExcel($SY, $SM, 1);
          $SelectedTime = PHPExcel_Shared_Date::FormattedPHPToExcel($SY, $SM, 1, substr($TV, 0, 2), substr($TV, 3, 5));
          $correctTime = $SelectedTime - $SelectedDate;
        } else {
          $correctTime = "";
        }
        return $correctTime;
      }

      ?>
    <script>
      alert("ダウンロード準備完了");
    //  history.back();
    </script>
</body>

</html>
