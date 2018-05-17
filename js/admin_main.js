$(document).ready(function() {
  $(".headB").on('click', function() {
    $(".headA").animate({
      width: "toggle"
    }, 200);
  });
});

function getvalue(frm) {
  var val = frm.ym.options[frm.ym.selectedIndex].value;
  window.location.href = 'adminDownload.php?YMs='+val;
}

// エンターキー防止
document.addEventListener('keydown', function(event) {
  if (event.keyCode === 13) {
      event.preventDefault();
  }
}, true);

function getQuerystring(key, default_){
  if (default_==null) default_="";
  key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
  var qs = regex.exec(window.location.href);
  if(qs == null)
    return default_;
  else
    return qs[1];
}

function select(){
  var page_value = getQuerystring('page');

  if(page_value==""){
    page_value = 1;
  }
    $.ajax({
        type: "POST",
        url: "adminSearch.php?page="+page_value,
        data : {"user_name": $("#user_name").val(),
                "YMs": $("#ym").val(),
                "userAllData": [userAllData],
                "monthlyAllData": [monthlyAllData],
                "dailyAllData": [dailyAllData]},//urlに送る Parameter
        success: function(datas){
          $("#refresh").html(datas); //戻り値 -> テーブルに出力
        },
        error: function(xhr, status, error) {
          alert(error);
        }
    });
}
