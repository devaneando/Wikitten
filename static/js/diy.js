function timeLoop(){
    Array.from(document.getElementsByClassName("wikiTimer")).forEach(e => {
        if("downtime" in e.dataset){
            e.innerHTML = e.dataset.pre + TimeDown(e.dataset.downtime)
        }else if("time" in e.dataset){
            e.innerHTML = e.dataset.pre + TimeUp(e.dataset.time)
        }
    })
}
window.onload=function(){
    if(document.getElementsByClassName("wikiTimer").length > 0){
        timeLoop()
        setInterval(timeLoop,1000);
    }
    const today = new Date();
    Array.from(document.getElementsByClassName("wikiTimer")).forEach(e => {
        if("lunarBirth" in e.dataset) {
            LunarBirthDay(today, e)
        }
    })
}
function TimeDown(endDateStr) {
    //结束时间
    var endDate = new Date(endDateStr);
    //当前时间
    var nowDate = new Date();
    //相差的总秒数
    var totalSeconds = parseInt((endDate - nowDate) / 1000);
    //天数
    var days = Math.floor(totalSeconds / (60 * 60 * 24));
    if(days < 0){
        return "时间到!";
    }
    //取模（余数）
    var modulo = totalSeconds % (60 * 60 * 24);
    //小时数
    var hours = Math.floor(modulo / (60 * 60));
    modulo = modulo % (60 * 60);
    //分钟
    var minutes = Math.floor(modulo / 60);
    if(minutes < 0){
        return "时间到!";
    }
    //秒
    var seconds = modulo % 60;
    //输出到页面
    return days + "天" + hours + "小时" + minutes + "分钟" + seconds + "秒";
}

function TimeUp(startDateStr) {
    //起始时间
    var startDate = new Date(startDateStr);
    //当前时间
    var nowDate = new Date();
    //相差的总秒数
    var totalSeconds = parseInt((nowDate - startDate) / 1000);
    //天数
    var days = Math.floor(totalSeconds / (60 * 60 * 24));
    //取模（余数）
    var modulo = totalSeconds % (60 * 60 * 24);
    //小时数
    var hours = Math.floor(modulo / (60 * 60));
    modulo = modulo % (60 * 60);
    //分钟
    var minutes = Math.floor(modulo / 60);
    //秒
    var seconds = modulo % 60;

    var ret = days + "天" + hours + "小时" + minutes + "分钟" + seconds + "秒";
    //下次周年
    var nextDay;
    startDate.setYear(nowDate.getFullYear());
    if(startDate < nowDate) {
        startDate.setYear(nowDate.getFullYear() + 1);
    }
    nextDay = Math.floor(parseInt((startDate - nowDate) / 1000) / (60 * 60 * 24));
    if(nextDay <= 100) {
        ret = ret + " 距周年:" + nextDay + "天";
    }
    return ret;
}

function LunarBirthDay(todaySolar, e){
    const tmp = e.dataset.lunarBirth.match(/\d+/g);
    if(tmp.length < 2) {
        return "";
    }
    const todayLunar = Lunar.fromDate(todaySolar);
    let lunarYear = todayLunar.getYear();
    const lunarMonth = parseInt(tmp[0]);
    const lunarDay = parseInt(tmp[1]);
    if(Math.abs(todayLunar.getMonth()) > lunarMonth || (Math.abs(todayLunar.getMonth()) === lunarMonth && todayLunar.getDay() > lunarDay)) {
        lunarYear = todayLunar.getYear() + 1;
    }
    //下次农历生日
    const birthLunar = Lunar.fromYmd(lunarYear, lunarMonth, lunarDay);
    //下次农历生日的公历日期
    const birthSolar = birthLunar.getSolar();

    //下次周年
    const nextDay = Math.floor(parseInt((new Date(birthSolar.toString()) - todaySolar) / 1000)  / (60 * 60 * 24));

    if(nextDay <= 100) {
        e.style.color= "rgb(179,125,18)";
    } else {
        e.style.color= "rgb(31, 189, 166)";
    }
    e.innerHTML = e.dataset.pre + "【" + birthLunar.toString() + "】【" + birthSolar.toString() + "】【" + nextDay + "天】"
}