<!DOCTYPE html>
<html>
    <head>
        <script src="https://kit.fontawesome.com/8509acf5d6.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <style>
            .fa-spin, .glyphicon-spin {
                -webkit-animation: spin 1000ms infinite linear;
                animation: spin 1000ms infinite linear;
            }
            @-webkit-keyframes spin {
                0% {
                    -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                100% {
                    -webkit-transform: rotate(359deg);
                    transform: rotate(359deg);
                }
            }
            @keyframes spin {
                0% {
                    -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                100% {
                    -webkit-transform: rotate(359deg);
                    transform: rotate(359deg);
                }
            }
        </style>
    </head>
    <body>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Text</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">File</a>
        </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="input-group mb-3">
                <input id="text_input" name="text_input" type="text" class="form-control" placeholder="WhatsApp text message"  >
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" name="text_button">Send</button>
                </div>
            </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file_input" id="file_input">
                        <label class="custom-file-label" for="inputGroupFile04" name="file_label" id="file_label">Choose file</label>
                        <input type="hidden" id="file_url" name="file_url">
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04">Send</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <script>

            var instance = 'instance36739'
            var token = '?token=q7l76fhmhclcs2ua' 
            var chat_api_url = 'https://eu34.chat-api.com/'
            $url = chat_api_url + instance;
            var phoneNumber = <?php echo "27" . substr($_REQUEST['number'], 1); ?>;
            var time = new Date();


            // send test message 
            $("button").click(function(){
                var text_input = document.getElementById("text_input").value;
                var file_input = document.getElementById("file_input").value;
                
                if(text_input != ""){
                
                    var data = {
                        phone: phoneNumber,
                        body: text_input, // Message
                        chat_date: time.getTime(), // time of message sent
                    };
                    var url = $url + '/message' + token;
                    // Send a request
                    $.ajax(url, {
                        data : JSON.stringify(data),
                        contentType : 'application/json',
                        type : 'POST'
                    });
                    document.getElementById("text_input").value = "";
                }
                if(file_input != ""){
                    var file_url = document.getElementById("file_url").value;
                    var file_name = document.getElementById("file_label").value;
                    console.log(file_name + "," + file_url);  // <-- uncomment for debugging
                    var url =  $url + '/sendFile' + token;
                    var data = {
                        phone: phoneNumber,
                        body: file_url,
                        filename: file_name,
                    };
                    // Send a request
                    $.ajax(url, {
                        data : JSON.stringify(data),
                        contentType : 'application/json',
                        type : 'POST'
                    });

                    $("#file_label").text("Choose file");
                    deleteFile(file_url);

                }

                console.log(data); // <-- uncomment for debugging
            });

            $(document).on('change', '#file_input', function(){

            var name = document.getElementById("file_input").files[0].name;
            var form_data = new FormData();
            var ext = name.split('.').pop().toLowerCase();
            if(jQuery.inArray(ext, ['gif','png','jpg','jpeg','pdf','mp4']) == -1) {
                alert("Invalid File");
            }
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById("file_input").files[0]);
            var f = document.getElementById("file_input").files[0];
            var fsize = f.size||f.fileSize;
            if(fsize > 2000000){
                alert("File Size is very big");
            }
            else{
                form_data.append("file_input", document.getElementById('file_input').files[0]);

                $.ajax({
                    url:"upload.php",
                    method:"POST",
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend:function(){
                        console.log("Uploading file");  // <-- uncomment for debugging
                        $("#file_label").html("Uploading file <i class='fas fa-spinner fa-spin'></i>");
                    },   
                    success:function(data){
                        console.log("File uploaded");  // <-- uncomment for debugging
                        var file_url = data;
                        var my_filename = file_url.substring(file_url.lastIndexOf('/')+1);
                        $("#file_label").text(my_filename);
                        document.getElementById("file_url").value = file_url;
                        document.getElementById("file_label").value = my_filename;

                    }
                    
                });
            }
        });

        function deleteFile(file_url)
        {
            var r = true;
            //var r = confirm("Are you sure you want to delete this Image?")
            if(r == true)
            {
                $.ajax({
                url: 'delete.php',
                data: {'file' : file_url },
                success: function (response) {
                    console.log("File " + file_url + " deleted");
                },
                error: function () {
                    console.log("Error while trying to delete file");
                }
                });
            }
        }

        </script>
    </body>
</html>