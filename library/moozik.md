## 我做了一些修改

1. dark、light按钮用来切换两个皮肤
2. 隐藏了md后缀
3. 设定`_`开头的目录和文件为隐藏，必须登陆或设置为公开wiki才能看到
4. 换了一套markdown解析程序（typecho的解析器），支持在markdown中插入html片段
5. 显示文件修改时间

---

## 添加代码
```
//你的代码
```

---

## 添加js特效

**2050年中华民族伟大复兴**
<h6 id="country" style="color:red;"></h5>


[https://moozik.cn](https://moozik.cn)
**我的博客已运行**
<h6 id="blog" style="color:orange;"></h5>

!!!

<script>
//可以插入script片段来丰富你的wiki，比如加入小工具
function TimeDown(id, endDateStr) {
    //结束时间
    var endDate = new Date(endDateStr);
    //当前时间
    var nowDate = new Date();
    //相差的总秒数
    var totalSeconds = parseInt((endDate - nowDate) / 1000);
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
    //输出到页面
    document.getElementById(id).innerHTML = "还剩:" + days + "天" + hours + "小时" + minutes + "分钟" + seconds + "秒";
    //延迟一秒执行自己
    setTimeout(function () {
        TimeDown(id, endDateStr);
    }, 1000)
}function TimeUp(id, startDateStr) {
    //结束时间
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
    //输出到页面
    document.getElementById(id).innerHTML = days + "天" + hours + "小时" + minutes + "分钟" + seconds + "秒";
    //延迟一秒执行自己
    setTimeout(function () {
        TimeUp(id, startDate);
    }, 1000)
}
TimeDown('country','2050-10-01 00:00:00');
TimeUp('blog','2016-08-01 00:00:00');
</script>
!!!
