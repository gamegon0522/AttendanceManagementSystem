<!DOCTYPE html>
<html lang="jp" dir="ltr">
<!--
  180508
  シフト画面(ログインしたユーザー用)
  Html/css：ユンへリン
  機能構築：jhkim
-->
<head>
  <script src="https://use.fontawesome.com/926fe18a63.js"></script>
  <!-- javascript jquery 宣言 -->
  <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
  <!-- javascript(node.js) Firebase, Auth 宣言-->
  <script src="https://www.gstatic.com/firebasejs/5.0.2/firebase.js"></script>
  <script type="text/javascript" src="js/db.js"></script>

  <script>
    $(document).ready(function(){
      $(".headB").on('click', function(){
        $(".headA").animate({width:"toggle"}, 200);
      });
    });

    /* Enter Submit Event Remove */
    document.addEventListener('keydown', function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        }
    }, true);
  var auth, database;
  var userInfo;
  </script>

  <meta charset="utf-8">
  <link rel="stylesheet" href="css/timeSheet_style.css">
  <link rel="shortcut icon" href=""> <!-- remove favico.ico error -->

  <title>シフト履歴</title>

  <?php
    //selected YearMonth
    date_default_timezone_set('Asia/Tokyo'); //  default地域設定
    $monthlyDay = date("t"); //一か月の最終日
    $month = date("m"); // 現在の月
  	$year = date("Y"); // 現在の年
    $strtotime = $year."-".$month."-1"; //毎月1日の曜日を求めるための変数
    $dailyInt = date('w', strtotime($strtotime)); // date関数は0~6の数字をreturnする
    $daily = array('日','月','火','水','木','金','土');
    $workarray = array();
  ?>
</head>

<body>
<!-- Loading-image -->
<div id="loading"><img id="loading-image" src="image/Loading.gif" alt="Loading..." /></div>

<div class="overlay">
  <header>
    <div class="container">
      <div class="container-small">
        <button type="button" class="headB">
          <span class="fa fa-bars"></span>
        </button>
      </div>
      <!-- ページ移動メニュー -->
      <nav class="headA">
        <ul id="menu">
            <li><a href="punch.php">出・退勤</a></li>
            <li><a href="memberInfo/mMemInfo.html">個人情報</a></li>
            <li><a href="timeSheet.php">履歴</a></li>
            <li><a href="./admin.php" id="admin">管理者</a></li>
            <li><a href="log/mLogout.html" id="login">ログアウト</a></li> <!-- log/ == ./log/-->
        </ul>
      </nav>
    </div>
  </header>
</div>
  <b>シフト履歴</b>
  <hr>
  <div class="timeSheet-form">
  <!-- Excel Export Form -->
  <form action="excelExport.php" method="post">
  <!-- Ajax cssの為の基本HTML構造設定 -->
  <div class=buttons-container>
    <div class="select-form" id="SF">
        <select name="YearMonth" id="YearMonth">
          <option selected></option>
        </select>
        <button type="button" name="search" id="search" class="search-btn">検索</button>
    </div>
    <div class="button-form" id="BF">
      <input type=button name="modify" id="modify" onclick='DBmodify()' class="modify-btn" value=""/>
      <input type=submit name="ExcelExport" class="download-btn" value="DL"></input>
    </div>
  </div>
    <!-- Ajax table -->
    <!-- 以下のテーブルの内容(Attendances_dailyだけ)は現在の運営には特にいらないが、開発した時のテスト用テーブルなので残します -->
    <table id="refresh">
      <tr>
        <th class="small-cell">日</th>
        <th class="small-cell">曜</th>
        <th>勤務地</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>休憩</th>
        <th>残業</th>
      </tr>
      <tr>
        <td>ロ</td>
        <td>ー</td>
        <td>ディ</td>
        <td>ン</td>
        <td>グ</td>
        <td>中</td>
        <td>…</td>
      </tr>
      <input type=hidden name="workdata" value=<?=json_encode($workarray) ?>></input>
      <input type=hidden name="YM" value=<?=$year.$month ?>></input>
      <tr class="memo-cell">
        <th colspan="2">備考</th>
        <td colspan="4"><textarea name="note" id="note" rows="8" cols="80"></textarea></td>
        <td><input type=button value="備考セーブ" onclick='SaveNote()' class="note-btn"></input></td>
      </tr>
    </table>
    </form>
  </div>

  <script>
  var Attendances_dailyRef = "";
  var Attendances_monthlyRef = "";
  var UsersRef = "";
  var SelectYM = "";
  var Notevalue = "";

  // Firebase Lodaing....
  $(window).load(function() {
    /* UserAuth Check */
    <?php if(empty($_GET["AuthUser"])) { ?> //$_GET Empty Check(Menuから入った場合)
      auth.onAuthStateChanged(function(user){ //Authentication ChangeCheck(Sessioncheck = Login Check)
        if (user) { //Authentication User is signed in. → don't need Authentication
          userInfo = user.uid;
          Pageloading();
        } else { //Authentication No user is signed in. → need Authentication
          alert("正しい接近ではありません! 初期画面に移動します!");
          window.location.href = "log/mLogin.html"; //Login画面(Test:index.php)
        }
      })
    <?php } else { ?> //管理者画面から入った場合
      userInfo = "<?=$_GET["AuthUser"]?>";
      Pageloading();
    <?php } ?>

    function Pageloading() {
      //update and refresh
      Attendances_dailyRef = database.ref('Attendances_daily/' + userInfo );
      Attendances_monthlyRef = database.ref('Attendances_monthly/' + userInfo );
      UsersRef = database.ref('Users/' + userInfo );
      SelectYM = <?=$year.$month?>; //SelectYM Save
      Notevalue = "";

      /* Pageloading start */
      Attendances_monthlyRef.on('value', function(data){
        Attendances_monthly = data.val();
        Notevalue = Attendances_monthly[<?=$year.$month?>]["attendances_memo"]; //Notevalue save
        $.ajax({
            type: "POST", //データ送信形式
            url: "timeSheetSelect.php", //請求される場所 -> つまり、データを取る場所です
            data : {"YearMonth": <?=$year.$month?>, //洗濯した年月 -> JSON形式
                    "Attendances_monthly": Attendances_monthly
                   }, //urlに送る Parameter
            success: function(datas){
              $("#SF").html(datas); //戻り値 -> テーブルに出力
            },
            error: function(xhr, status, error) {
              alert(error);
            }
        })
      })

      UsersRef.on('value', function(data) {
      Users = data.val();
      authority_id = Users.authority_id;
      if(authority_id == 0) { //使用者の場合、メニューから管理者が見えないようにする
        var lis = document.getElementsByTagName('li');
        document.getElementById('menu').removeChild(lis[3]);
      }

      $.ajax({
          type: "POST", //データ送信形式
          url: "timeSheetButton.php", //請求される場所 -> つまり、データを取る場所です
          data : {
                  "Users": Users
                 }, //urlに送る Parameter
          success: function(datas){
            $("#BF").html(datas); //戻り値 -> テーブルに出力
          },
          error: function(xhr, status, error) {
            alert(error);
          }
        })
      })

      Attendances_dailyRef.on('value', function(data){
      Attendances_daily = data.val();
        $.ajax({
          type: "POST", //データ送信形式
          url: "timeSheetSearchTable.php", //請求される場所 -> つまり、データを取る場所です(テーブルの中身が必要)
          data : {"YearMonth": <?=$year.$month?>, //洗濯した年月 -> JSON形式
                  "Attendances_daily": Attendances_daily,
                  "Notevalue": Notevalue
                 }, //urlに送る Parameter
          success: function(datas){
            $("#refresh").html(datas); //戻り値 -> テーブルに出力
          },
          error: function(xhr, status, error) {
            alert(error);
          }
        })
      $('#loading').hide(); //Loading End
      });
      /* Pageloading END */
    }
  });

  /* Ajax for Search - TimeSheetType */
  $(document).on('click', '.search-btn', function(){
      if(document.getElementById("modify")) { //ユーザーの場合の確認
        document.getElementById("modify").value = "修正";
      }
      SelectYM = document.getElementById("YearMonth").value; //SelectYM Save
      Notevalue = Attendances_monthly[SelectYM]["attendances_memo"]; //Notevalue save
      $.ajax({
          type: "POST", //データ送信形式
          url: "timeSheetSearchTable.php", //請求される場所 -> つまり、データを取る場所です
          data : {"YearMonth": $("#YearMonth").val(), //洗濯した年月 -> JSON形式
                  "Attendances_daily": Attendances_daily,
                  "Notevalue": Notevalue
                 }, //urlに送る Parameter
          success: function(datas){
            //alert("Success");
            //alert(datas);
            $("#refresh").html(datas); //戻り値 -> テーブルに出力
          },
          error: function(xhr, status, error) {
            alert(error);
          }
      })
  })

  /* Ajax For Modify - TimeSheetType */
  $(document).on('click', '.modify-btn', function(){
    var buttonText = document.getElementById("modify").value; //Onclickより早く作動しますのでLocal変数に作りました。
    if(buttonText == "修正") { //修正の時
      document.getElementById("modify").value = "修正終了";
      $.ajax({
        type: "POST", //データ送信形式
        url: "timeSheetModifyTable.php", //請求される場所 -> つまり、データを取る場所です
        data : {"YearMonth": SelectYM, //選択した年月
                "Attendances_daily": Attendances_daily,
                "Notevalue": Notevalue
               }, //urlに送る Parameter
        success: function(datas){
          $("#refresh").html(datas); //戻り値 -> テーブルに出力
        },
        error: function(xhr, status, error) {
          alert(error);
        }
      })
    } else { //修正完了の時
    document.getElementById("modify").value = "修正";
    $.ajax({
        type: "POST", //データ送信形式
        url: "timeSheetSearchTable.php", //請求される場所 -> つまり、データを取る場所です
        data : {"YearMonth": SelectYM, //選択した年月  -> JSON形式
                "Attendances_daily": Attendances_daily,
                "Notevalue": Notevalue
               }, //urlに送る Parameter
        success: function(datas){
          $("#refresh").html(datas); //戻り値 -> テーブルに出力
        },
        error: function(xhr, status, error) {
          alert(error);
        }
      })
    }
  })
  </script>

  <script>
    /* Firebase Database update */
    function DBmodify() {
      var buttonText = document.getElementById("modify").value;
      if(buttonText == "修正終了") {
        <?php //print ALL monthlyDays
        for($i=1;$i<$monthlyDay+1;$i++) {
        if($i < 10) $i = "0".$i;
        ?>

        //null check
        if(document.getElementById("STid<?=$i?>") != null) {

          //修正文字形Check(正規表現式)
          var Pattern = /^([1-9]|[01][0-9]|2[0-9]|3[0-9])[:]([0-5][0-9])$/; //時間形式(24->39時間までに変更)
          var RTPattern = /^[0-9][:]([0-5][0-9])$/; //時間形式
          var ST = document.getElementById("STid<?=$i?>").value;
          var ET = document.getElementById("ETid<?=$i?>").value;
          var RT = document.getElementById("RTid<?=$i?>").value;
          if(Pattern.test(ST) && Pattern.test(ET) && RTPattern.test(RT)) {
            //修正
            var SelectYMD = SelectYM + '<?=$i?>';
            Attendances_dailyRef.update({
              [SelectYMD]: {
                start_time: document.getElementById("STid<?=$i?>").value,
                end_time: document.getElementById("ETid<?=$i?>").value,
                rest_time: document.getElementById("RTid<?=$i?>").value,
                workplace: document.getElementById("WPid<?=$i?>").innerHTML
              },
            });
          } else {
            alert("ログインしてください。");
          }
        }
        <?php
        } ?>
      }
    }

    /* Note Value Save */
    function SaveNote() {
      Attendances_monthlyRef.child(SelectYM).once("value", function(snap){ //一つのデータだけに入れる
        snap.ref.update({
            attendances_memo: $("textarea").val()
        })
      })
      alert("セーブ完了");
    }
  </script>
</body>
</html>
