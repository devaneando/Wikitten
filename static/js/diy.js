function timeLoop(flag){
    Array.from(document.getElementsByClassName("wikiTimer")).forEach(e => {
        if("downtime" in e.dataset){
            TimeDown(e, flag);
        }else if("time" in e.dataset){
            TimeUp(e, flag);
        }
    })
}
window.onload=function(){
    if(document.getElementsByClassName("wikiTimer").length > 0){
        timeLoop(true);
        setInterval(function () {
            timeLoop(false);
        },1000);
    }
    const today = new Date();
    Array.from(document.getElementsByClassName("wikiTimer")).forEach(e => {
        if("lunarBirth" in e.dataset) {
            LunarBirthDay(today, e)
        }
    })
    Notification.requestPermission((result)=>{
        console.log(result);
    });
}
function TimeDown(e,flag) {
    //结束时间
    var endDate = new Date(e.dataset.downtime);
    //当前时间
    var nowDate = new Date();
    //相差的总秒数
    var totalSeconds = parseInt((endDate - nowDate) / 1000);
    //天数
    var days = Math.floor(totalSeconds / (60 * 60 * 24));
    if(days < 0){
        e.innerHTML = e.title+ '['+e.dataset.downtime+' 时间到]';
        return;
    }
    //取模（余数）
    var modulo = totalSeconds % (60 * 60 * 24);
    //小时数
    var hours = Math.floor(modulo / (60 * 60));
    modulo = modulo % (60 * 60);
    //分钟
    var minutes = Math.floor(modulo / 60);
    if(minutes < 0){
        e.innerHTML = e.title+ '['+e.dataset.downtime+' 时间到]';
        return;
    }
    //秒
    var seconds = modulo % 60;
    e.innerHTML = e.title+ '['+days + "天" + hours + "小时" + minutes + "分钟" + seconds + "秒"+']';
    if (flag) {
        dayCheckAlert("倒计时提醒",e.title, days);
    }
}

function TimeUp(e,flag) {
    //起始时间
    var startDate = new Date(e.dataset.time);
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
    if (flag) {
        dayCheckAlert("周年提醒",e.title, nextDay);
    }
    if (nextDay < 100) {
        e.innerHTML = e.title+ '['+ret+']['+"*"+nextDay+'天]';
    }else{
        e.innerHTML = e.title+ '['+ret+']'
    }
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
    //生日当天
    if((Math.abs(todayLunar.getMonth()) === lunarMonth && todayLunar.getDay() === lunarDay))
    {
        e.style.color= "rgb(179,18,171)";
        e.innerHTML = e.title + "【" + Lunar.fromYmd(lunarYear, lunarMonth, lunarDay).toString() + "】【生日快乐！】";
        return;
    }

    if(Math.abs(todayLunar.getMonth()) > lunarMonth
        || (Math.abs(todayLunar.getMonth()) === lunarMonth && todayLunar.getDay() > lunarDay))
    {
        lunarYear += 1;
    }
    //下次农历生日
    for(let i=0;i<10;i++){
        if(LunarYear.fromYear(lunarYear).getMonth(lunarMonth).getDayCount() < lunarDay) {
            lunarYear += 1;
        }
    }
    const birthLunar = Lunar.fromYmd(lunarYear, lunarMonth, lunarDay);
    //下次农历生日的公历日期
    const birthSolar = birthLunar.getSolar();

    //下次周年
    const nextDay = Math.ceil(parseInt((new Date(birthSolar.toString()) - todaySolar) / 1000)  / (60 * 60 * 24));
    if(nextDay <= 100) {
        e.style.color= "rgb(179,125,18)";
    } else {
        e.style.color= "rgb(31, 189, 166)";
    }
    dayCheckAlert("生日提醒",e.title, nextDay);
    e.innerHTML = e.title + "【" + birthLunar.toString() + "】【" + birthSolar.toString() + "】【" + nextDay + "天】"
}

function dayCheckAlert(title,body,day) {
    console.log(title,body,day);
    if (day > 100) {
        return;
    }
    if (day === 0) {
        desktopAlert(title, body + "【时间到】")
        return;
    }
    if (day === 30 || day <= 7) {
        desktopAlert(title, body + "【"+day+"天】")
    }
}
function desktopAlert(title,body) {
    Notification.requestPermission((result)=>{
        // 只有当result为granted时，才会执行下方的new Notification代码，denied和default不会执行
        const notification = new Notification(title,{
            dir: "ltr",
            body: body,
            icon: "https://wiki.m00zik.com/static/img/favicon.png",
            image: "https://m00zik.com/usr/uploads/logo3.jpg",
            sticky: true,
            requireInteraction: true,
            renotify: false,
        });
    });
}
