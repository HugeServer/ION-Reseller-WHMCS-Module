<h1>Bandwidth Graph{$test}</h1>
<div style="margin: 30px 10px;" class="breadcrumb">{$breadcrumbnav|replace:' >':' / '} / {$menu}</div>
<img id="gimg" width="65%" src="modules/servers/ion/graph.php?period=hour&pid={$pid}&title={$title}">
<br><br>
<b><label for="period">Period:</label></b>
<select style="width: 200px;padding: 5px;margin-bottom: 35px;" id="period" onchange="showGraph()">
    <option value="hour">Hour</option>
    <option value="day">Day</option>
    <option value="week">Week</option>
    <option value="month">Month</option>
</select><br><br>

<script>
    function showGraph(){
        var periodel = document.getElementById("period");
        var gimg = document.getElementById("gimg");
        var url;
        var period = periodel.options[periodel.selectedIndex].value;
        gimg.src = "modules/servers/ion/graph.php?pid={$pid}&period=" + period + "&title={$title}";
    }
</script>