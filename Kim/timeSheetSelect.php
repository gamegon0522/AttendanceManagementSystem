<?php
  /*
    自分のシフト初期画面設定(Select-form)
    180509
    jhkim
  */
  $YMs = $_POST["Attendances_monthly"]; //配列送信
  $selectYM = $_POST["YearMonth"];
?>
<div class="select-form" id="SF">
      <select name="YearMonth" id="YearMonth">
        <?php //print Year and Month
        foreach($YMs as $key=>$value) {
          if($key == $selectYM) { ?>
            <option selected><?=$key?></option>
          <?php } else { ?>
            <option><?=$key?></option>
          <?php }
        } ?>
      </select>
      <button type="button" name="search" id="search" class="search-btn">検索</button>
</div>
