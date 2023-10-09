<div class="full-screen column center">
    <h1 class="text-banner center-text"> <?= $header ?> </h1>
    <h1 class="text-header"> <?= $sub_header ?> </h1>
</div>
<script>
    var conn = new WebSocket('ws://192.168.1.9:8084');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        var data = JSON.parse(e.data);
        console.log(data);
    };

    function send(){
        conn.send(JSON.stringify("fdsdsf"));
    }
    
</script>