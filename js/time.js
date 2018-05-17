

        var checkintime;
        var checkouttime;
        var intervaltime;

        // Time Setting
        function clock() {
                var data = new Date();

                // 時間と関連された情報を保存します。
                hours = data.getHours();
                minutes = data.getMinutes();
                seconds = data.getSeconds();
                timeStr = ((hours < 10) ? "0" : "") + hours;
                timeStr += ((minutes < 10) ? ":0" : ":") + minutes;
                timeStr += ((seconds < 10) ? ":0" : ":") + seconds;

                // フォームの時間を表示する入力欄に文字列を出力します。
                document.clock.time.value = timeStr;

                // 日付と関連された情報を保存します。
                months = data.getMonth();
                days = data.getDate();
                years = data.getFullYear();
                dateStr = years;
                dateStr += ((months < 10) ? "/0" : "/") + months;
                dateStr += ((days < 10) ? "/0" : "/") + days;


                // フォームの日付を表示する入力欄に文字列を出力します。
                document.clock.date.value = dateStr;

                // １秒ごとに日付と時間が変わる
                Timer = setTimeout("clock()", 1000);

                //console.log ( hours  + ((minutes < 10) ? ":0" : ":") + minutes);


                // ここから神立打ち //

                //----時間の割り当て----//

                var Array = new Array ["00","15","30","45"];



                if(minutes >= 00 && minutes <= 14){
                  checkintime = (hours + ":" + Array[1]);
                }

                if(minutes >= 15  && minutes <= 29){
                  checkintime = (hours + ":" + Array[2]);
                }

                if(minutes >= 30 && minutes <= 44){
                  checkintime = (hours + ":" + Array[3]);
                }

                if(minutes >= 45 && minutes <= 59){
                  checkintime = (hours + 1 + ":0" + Array[0]);
                }
//----------------------------------------------------------------------------//
                if(minutes >= 00 && minutes <= 14){
                  checkouttime = (hours + ":0" + Array[0]);
                }

                if(minutes >=15  && minutes <= 29){
                  checkouttime = (hours + ":" + Array[1]);
                }

                if(minutes >= 30 && minutes <= 44){
                  checkouttime = (hours + ":" + Array[2]);
                }

                if(minutes >= 45 && minutes <= 59){
                  checkouttime = (hours + ":" + Array[3]);
                }

                if(minutes >= 30 && minutes <= 59 && hours == 9){
                  checkintime = (hours + 1 + ":0" + Array[0]);
                }

                //console.log(faiz_1,faiz_2,faiz_3,faiz_4);
                //console.log(faiz_5,faiz_6,faiz_7,faiz_8);

}

        // end of Time setting -->
