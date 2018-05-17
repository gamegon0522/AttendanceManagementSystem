<!DOCTYPE html>
  <html>
    <head>
    </head>
    <body>
        <?php
        /*
          180501
          Excel出力(勤務表)
          jhkim
        */

        /* $_POSTで値を持つ */
        $UserData = json_decode($_POST["Users"], true); //User Data
        $workdata = json_decode($_POST['workdata'] ,true); //User Dayly Data
        $YMs = $_POST['YM']; //TimeSheet Selected Year and Month

        $SelectedYear = substr($YMs, 0, 4);
        $SelectedMonth = substr($YMs, 4, 6);

        require_once "Classes/PHPExcel.php";// PHPExcel.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.
        $objPHPExcel = new PHPExcel();
        require_once "Classes/PHPExcel/IOFactory.php";// IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.
        $filename = "workSystemForm.xlsx";// 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.

        try {
        $objReader=PHPExcel_IOFactory::createReaderForFile($filename); //엑셀 Type에 맞는 Read객체를 만듬
        //$objReader->setReadDataOnly(true); // Read only
        $objPHPExcel = $objReader->load($filename); // 엑셀 파일을 읽는다.
        $objPHPExcel->setActiveSheetIndex(0); // 첫번째 시트를 선택
        $sheet = $objPHPExcel->getActiveSheet(); // 선택한 시트에 대한 작업용 변수선언

        /* User Data Input */
        $sheet -> setCellValue('C3', PHPExcel_Shared_Date::FormattedPHPToExcel($SelectedYear, $SelectedMonth, 1));
        $sheet -> setCellValue('C4', $UserData['department']);
        $sheet -> setCellValue('C5', $UserData['name_kana']);
        $sheet -> setCellValue('C6', $UserData['user_name']);
        $sheet -> setCellValue('C7', $UserData['user_id']);
        $sheet -> setCellValue('C8', $UserData['division']);
        $sheet -> setCellValue('C9', $UserData['workplace']);
        $sheet -> setCellValue('C10', $UserData['occupation']);

        /* User Daily Data Input */
        $cellint = 13;

        foreach($workdata as $value) { //workdata export
          $sheet -> setCellValue('B'.$cellint, $value[1]); // day of the week
          $sheet -> setCellValue('C'.$cellint, $value[2]); // workplace
          $sheet -> setCellValue('E'.$cellint, CorrectTime($value[3], $SelectedYear, $SelectedMonth)); // starttime
          $sheet -> setCellValue('F'.$cellint, CorrectTime($value[4], $SelectedYear, $SelectedMonth)); // endtime
          $sheet -> setCellValue('G'.$cellint, $value[5]); // resttime
          $sheet -> getStyle('F'.$cellint) -> getNumberFormat() -> setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);// View Style
          $cellint++;
        }
        $sheet -> setCellValue('A46', $_POST['note']); //note
        $sheet -> mergeCells('A46:O48'); //A46~O48まで
        $sheet->getStyle('A46:O48')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT); //Horizontal_left
        $sheet -> setTitle($YMs); //sheet name

        /* Export Excel File */
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean(); //Clean (erase) the output buffer and turn off output buffering!!!!
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$YMs.'_Workschedule.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
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
    </body>
  </html>
