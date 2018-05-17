<!DOCTYPE html>
<html lang="jp" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://use.fontawesome.com/926fe18a63.js"></script>
<script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
<script src="js/time.js"></script>

<link rel="stylesheet" href="css/punch_style.css">

<script>
    $(document).ready(function(){
    $(".headB").on('click', function(){
    $(".headA").animate({width:"toggle"}, 200);
    });
    });

</script>


<style>
.time:nth-of-type(1) {
  margin-bottom: 10px;
}
.time{
  font-size: 35px;
  border:0 none;
  margin-top: 30%;
  background-color: transparent;
  color: white;
  width: 100%;
  margin: 0;
  padding: 0;
  text-align: center;
}
</style>
</head>
<body onload="clock()" >
        <div class="overlay">
            <header>
			    <div class="container">
                <div class="container-small">
                <button type="button" class="headB">
                <span class="fa fa-bars"></span>
            </button>
        </div>
    <nav class="headA">
        <ul>
            <li><a href="punch.php">出・退勤</a></li>
            <li><a href="./memberInfo/mMemInfo.html"> 個人情報</a></li>
            <li><a href="timeSheet.php">履歴</a></li>
            <li><a href="admin.php" id="admin">管理者</a></li>
            <li><a href="./log/mLogout.html" id="login">ログアウト</a></li>
        </ul>
    </nav>
</div>
</header>
</div>


    <div class="Login"><a href="register_update.html" id="user_name"><h2>名前</h2></a></div>

	<button class="start-circle" onclick="OnButtonClick() ; StateChange() ; updatetime()"  id="button-state">
    <form name="clock">

              <input type="text" class="time" name="time" value="" readonly>  <br>

              <input type="text" class="time" name="date" value="" readonly>


    </form>

    <p class="check" id="check">出勤</p>

    </button>


<script language="javascript" type="text/javascript">

    var clickNumber = 0;
	//element.classList.add("start-circle");

        function OnButtonClick() {
            console.log(timeStr);

        }

		function StateChange() {

			clickNumber++;

			if((clickNumber % 2) == 0){
				var element = document.getElementById("button-state").className="start-circle";
				document.getElementById("check").textContent = "出勤";
        alert("あなたの退勤時間は" + checkouttime + "です！");
			}else{

				var element = document.getElementById("button-state").className="end-circle";
				document.getElementById("check").textContent = "退勤";
        alert("あなたの出勤時間は" + checkintime + "です！");
			}
    }


    </script>



    <script src="https://www.gstatic.com/firebasejs/5.0.2/firebase.js"></script>

    <script type="text/javascript" src="js/db.js"></script>

  <script>
  var firebaseEmailAuth; //ファイアベースemail認証モジュールのグローバル変数
  var firebaseDatabase; //ファイアベースdbモジュールのグローバル変数
  var user_name; //ユーザ名
  var loginUserKey; //ログインしたユーザーの親key
  var userInfo; //ログインしたユーザーの情報object type
  var comment; //ユーザが書いた文章を保存

  //認証モジュールオブジェクトの読み込み
  firebaseEmailAuth = firebase.auth();
  //データベースモジュールオブジェクトの読み込み
  firebaseDatabase = firebase.database();

  //セッションチェック関数
  userSessionCheck();

  //ユーザがログインしているならいることを確認してくれる関数
  function userSessionCheck() {

    //ログインしている場合、 - ユーザがあれば、userを引数の値に渡してくれる。
    firebaseEmailAuth.onAuthStateChanged(function (user) {

      if (user) {

        //ルック - データベースに保存されたニックネームを現在接続されているuserのpk key値を利用して、インポート
        firebaseDatabase.ref("Users/" + user.uid).once('value').then(function (snapshot) {

          user_name = snapshot.val().user_name;   //ユーザニックネームは書き続けるそこからグローバル変数に割り当て
          loginUserKey = snapshot.key;  //ログインしたユーザのkeyも書き続けるので、グローバル変数に割り当て
          userInfo = snapshot.val(); //snapshot.val（）にuserテーブルに対応するオブジェクト情報が超えています。userInfoに代入！

     var  authority_id = snapshot.val().authority_id;
     console.log(loginUserKey);
     console.log(authority_id);
     console.log(user_name);

          if(authority_id == 1) {
             console.log(user_name);
            //JavaScriptのdom選択を通したナビゲーションメニューのエレメントを変更接触
            document.getElementById("admin").href = "./admin.php";
            document.getElementById("login").textContent = "ログアウト　管理者";
            document.getElementById("login").href = "./log/mLogout.html";
            document.getElementById("user_name").textContent =  snapshot.val().user_name + " 様";
            document.getElementById("user_name").href = "./memberInfo/mMemInfo.html";

          } else if(authority_id == 0){　//管理者　authority_id　が　0なら　出ない
            document.getElementById("login").textContent = "ログアウト 利用者";
            document.getElementById("login").href = "./log/mLogout.html";
            document.getElementById("user_name").textContent =  snapshot.val().user_name + " 様";
            document.getElementById("user_name").href = "./memberInfo/mMemInfo.html";
            console.log("2"+authority_id);

             //使用者の場合、メニューから管理者が見えないようにする
            var lis = document.getElementsByTagName('li');
            document.getElementById('menu').removeChild(lis[3]);

          } else {
            alert("Please Login");
            document.getElementById("user_name").href = "./log/mLogin.html";
          }

          // uidはdbでuserテーブルの各オブジェクトのpkだが、この値は、認証から生成されたユーザ名のpk値と同じである。*重要！

          return true
        });

      } else {
              //$('#user_name').val("ログインしてから利用できます。");
              document.getElementById("user_name").textContent =  "ログイン";
              document.getElementById("user_name").href = "./log/mLogin.html";
        return false
      }
    });
  }



  //----------出勤・退勤時間のアップデート----------//
  database = firebase.database();

    function updatetime() {
        database.ref('Attendances_daily/zAF9bJSpx2Ok9qYdBVzp85dIXPy2/20180424/').set({
            start_time: checkintime,
            end_time: checkouttime,
        });
    }

  </script>

  </body>
</html>
