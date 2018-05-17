//냘짜 함수
var currentTimes;
var d = new Date();
var year = d.getFullYear();
var month = (d.getMonth() + 1);
var day = d.getDate();
var hours = d.getHours();
var min = d.getMinutes();
var sec = d.getSeconds();
//var currentTime = year + month + day + hours + min + sec;
currentTimes = year;
currentTimes += ((month < 10) ? "/0" : "") + month;
currentTimes += ((day < 10) ? "/0" : "") + day;
currentTimes += ((hours < 10) ? " 0" : " ") + hours;
currentTimes += ((min < 10) ? ":0" : ":") + min;
currentTimes += ((sec < 10) ? ":0" : ":") + sec;


        // Time Setting
        function clock() {
                data = new Date();

                // 시간과 관련된 정보를 저장한다.  時間と関連された情報を保存します。
                hours = data.getHours();
                minutes = data.getMinutes();
                seconds = data.getSeconds();
                timeStr = ((hours < 10) ? ":0" : "") + hours;
                timeStr += ((minutes < 10) ? ":0" : ":") + minutes;
                timeStr += ((seconds < 10) ? ":0" : ":") + seconds;

                // 폼의 시간을 표시하는 입력란에 문자열을 출력한다.　フォムの時間を表示する入力欄に文字列を出力します。
                document.clock.time.value = timeStr;

                // 일자와 관련된 정보를 저장한다.  日付と関連された情報を保存します。
                months = data.getMonth() + 1;
                days = data.getDate();
                years = data.getFullYear();
                dateStr = years;
                dateStr += ((months < 10) ? "/0" : "/") + months;
                dateStr += ((days < 10) ? "/0" : "/") + days;


                // 폼의 일자를 표시하는 입력란에 문자열을 출력한다.　フォムの日付を表示する入力欄に文字列を出力します。
                document.clock.date.value = dateStr;

                // １秒ごとに日付と時間が変わる
                Timer = setTimeout("clock()", 1000);
        }

        // end of Time setting -->
