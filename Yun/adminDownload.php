<?php
// Zipにしたいファイル名指定
$YMs = $_REQUEST["YMs"];
$dir = "./upload/";
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
      if(strpos($file, ".xlsx") !== false) {
        if(strpos($file, $YMs)!== false){
          $pathArray[] = "upload/".$file;
        }
      }
    }
    closedir($dh);
  }
}

// 処理制限時間を外す
set_time_limit(0);

// 取得ファイルをZipに追加していく
if(!empty($pathArray)){
  $count = count($pathArray);
  switch ($count) {
      case 0:
          // pathArrayが空
          echo "<script>alert('ダウンロード準備を押してください。'); history.back();</script>";
          error_reporting(0);
          break;

      case 1:
          // pathArrayが1つ
          download_single($pathArray);
          break;

      default:
          // pathArrayが2つ以上
          download_multiple($pathArray, $YMs);
          break;

  }
}else{
  echo "<script>alert('ダウンロード準備を押してください。'); history.back();</script>";
}

// pathArrayのファイルパス数によって処理分岐


// 終了
exit();

// 単一ファイルのダウンロード
function download_single($pathArray){

    // エラー処理
    if(count($pathArray) != 1){
        return;
    }

    // ファイルパスからファイル名を取得
    $filename = basename($pathArray[0]);

    // ダウンロードするダイアログを出力
    header('Content-Disposition: attachment; filename=' . $filename);

    // ファイルを読み込んで出力
    readfile($pathArray[0]);

}

// 複数ファイルのダウンロード（Zip圧縮）
function download_multiple($pathArray, $YMs){

    // Zipクラスロード
    $zip = new ZipArchive($pathArray);

    // Zipファイル名
    $zipFileName = "file_" . $YMs .'.zip';

    // Zipファイル一時保存ディレクトリ
    $zipTmpDir = '../pdf';

    // Zipファイルオープン
    $result = $zip->open($zipTmpDir . $zipFileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

    // エラー処理
    if ($result !== true) {
        // 失敗した時の処理
    }

    // zip用配列に追加していく
    foreach ($pathArray as $filepath) {

        // ファイルパスからファイル名を取得
        $filename = basename($filepath);

        if(file_exists($filepath)){
            $zip->addFromString($filename,file_get_contents($filepath));
        }else{
            echo "error => function download_multiple";
            // zip->closeが呼ばれないため直接returnはNG
            // return;
        }

    }

    // Zipを閉じる
    $zip->close();

    // ストリームに出力
    header('Content-Type: application/zip; name="' . $zipFileName . '"');
    header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
    header('Content-Length: '.filesize($zipTmpDir . $zipFileName));

    // ダウンロード
    echo file_get_contents($zipTmpDir . $zipFileName);

    // 一時ファイルを削除
    unlink($zipTmpDir.$zipFileName);

      for($i=0;$i<count($pathArray);$i++) {
        unlink($pathArray[$i]);
      }

}

?>
