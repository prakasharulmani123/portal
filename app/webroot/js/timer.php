<script type="text/javascript">
var counter = 0;
var timer = null;

function tictac(){
    counter++;
    $("#clock").html(counter);
}

function reset()
{
clearInterval(timer);
    counter=0;
}
function startInterval()
{alert('in');

timer= setInterval("tictac()", 1000);
}
function stopInterval()
{
    clearInterval(timer);
}
</script>

<div id="clock">0</div>
<div id="buttons" class="clear">
    <button id="start" onClick="tictac()">start</button>
    <button id="stop" onClick="stopInterval()">stop</button>
    <button id="reset" onClick="reset()">reset</button>
</div>