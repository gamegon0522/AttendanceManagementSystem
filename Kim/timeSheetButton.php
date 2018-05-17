<?php
  /*
  自分のシフト初期画面設定(Button-form)
  180509
  jhkim
  */
  $UserData = $_POST["Users"];
?>
<div class="button-form" id="BF">
    <!-- 修正ボタン  -->
    <?php if($UserData["authority_id"] == 1) { ?>
      <input type="button" name="modify" id="modify" onclick='DBmodify()' class="modify-btn" value="修正"/>
    <?php } ?>
    <input type=submit name="ExcelExport" class="download-btn" value="DL"></input>
    <input type=hidden name="Users" value=<?=json_encode($UserData) ?>></input>
</div>
