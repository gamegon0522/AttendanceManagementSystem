

var firebaseEmailAuth; //firebase email 認証 モジュール全域の変数
var firebaseDatabase; //firebase db モジュール全域の変数
var userInfo; //登録したユーザの情報. object タイプ
var loginUserKey;
var user;
var email;//
var user_id;
var user_name;
var name_kana;
var password;
var password2;
var birthday;
var workplace;
var start_time;
var end_time;
var departure_station;
var arrival_station;
var commutation_ticket;
var department;
var costs;
var rest_time;
var division;
var occupation;



  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyA3uuX2492PTvcZwatVdi3EcGLHqwyuHdM",
    authDomain: "commute-8778.firebaseapp.com",
    databaseURL: "https://commute-8778.firebaseio.com",
    projectId: "commute-8778",
    storageBucket: "commute-8778.appspot.com",
    messagingSenderId: "1044626464848"
  };
  //Firebase 初期化
  firebase.initializeApp(config);

  //認証モジュールobject読み込む
  firebaseEmailAuth = firebase.auth();
  user = firebase.auth().currentUser;
  //databaseモジュールobject読み込む
  firebaseDatabase = firebase.database();
