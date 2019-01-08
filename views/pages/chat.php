	<? kmh_print( explode(':',get_include_path()) ); ?>

    <div id="container">
        <div id="body">
            <div id="messages"></div>
            <input type="text" id="text" placeholder="Message .."
            	value="<?=$this->uri->segment(3)?"USER {$this->uri->segment(3)}":"GUEST"?>"
            	>
            <input type="text" id="recipient_id" placeholder="Recipient id ..">
            <button id="submit" value="POST">Send</button>
        </div>
        <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>

        <div class="alert alert-danger disconnected">disconnected</div>
        <div class="alert alert-success connected" style="display: none;">connected</div>
    </div>

    <script>
	    var conn = new WebSocket('ws://localhost:8282');
	    var client = {
	        user_id: '<?php echo $user_id; ?>',
	        recipient_id: null,
	        message: null,
	        broadcast: true,
	    };

	    conn.onopen = function(e) {
	        conn.send(JSON.stringify(client));
	        $('#messages').append('<font color="green">Successfully connected as user '+ client.user_id +'</font><br>');
	        $(".connected").show();
	        $(".disconnected").hide();
	    };

	    conn.onmessage = function(e) {
	        var data = JSON.parse(e.data);
			var currentdate = new Date();
			var datetime = "Last Sync: " + currentdate.getDate() + "/"
			                + (currentdate.getMonth()+1)  + "/"
			                + currentdate.getFullYear() + " @ "
			                + currentdate.getHours() + ":"
			                + currentdate.getMinutes() + ":"
			                + currentdate.getSeconds();
	        if (data.message) {
	            $('#messages').append(data.user_id + ' : ' + data.message + ' (' + datetime + ')<br>');
	        }
	    };

	    conn.onclose = function(e) {
	    	console.log(e);
	        $(".connected").hide();
	        $(".disconnected").show();
	    };

	    $('#submit').click(function() {
	        client.message = $('#text').val();
	        if ($('#recipient_id').val()) {
	            client.recipient_id = $('#recipient_id').val();
	        } else {
	        	client.recipient_id = null;
	        }
	        console.log( client );
	        conn.send(JSON.stringify(client));
	    });
    </script>