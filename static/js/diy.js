function timeLoop(){
    Array.from(document.getElementsByClassName("wikiTimer")).forEach(e => {
        if("downtime" in e.dataset){
            e.innerHTML = e.dataset.pre + TimeDown(e.dataset.downtime)
        }else{
            e.innerHTML = e.dataset.pre + TimeUp(e.dataset.time)
        }
    })
}
window.onload=function(){
    if(document.getElementsByClassName("wikiTimer").length > 0){
        timeLoop()
        setInterval(timeLoop,1000);
    }
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
