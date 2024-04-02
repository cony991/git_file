// 側邊欄的部分
/* Set the width of the side navigation to 250px and the left margin of the page content to 250px and add a black background color to body */
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
  }
  
  /* Set the width of the side navigation to 0 and the left margin of the page content to 0, and the background color of body to white */
  function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
    document.body.style.backgroundColor = "white";
  }

  // 以下是選項卡(Tabs)的部分
  

  function openManager(managerName){
    var i, tabContent;
     tabContent = document.getElementsByClassName("tabContent");
    for (i = 0; i < tabContent.length ; i++){
      tabContent[i].style.display = "none";    
    }   
    document.getElementById(managerName).style.display = "block";
   
  }
  
  
  // 後台會員管理系統
    // newData是read用到的
    // u_id  是update
    var newData= [];
    var u_id;
    $(function(){       
        
            if (getCookie("UID02") != "") {
                //UID02存在, 傳遞至後端api 判斷是否合法
                var dataJSON = {};
                dataJSON["UID02"] = getCookie("UID02");
                console.log(JSON.stringify(dataJSON));
                $.ajax({
                    type: "POST",
                    url: "api/manager/manager-Check_UID-api.php",
                    data: JSON.stringify(dataJSON),
                    dataType: "json",
                    success: showdata_Check_UID,
                    error: function () {
                        alert("error-api/manager/manager-Check_UID-api.php");
                    }
                });
            }else{
                location.replace("login.html");
            }

            // 登出
            $("#logout_btn").click(function(){
                setCookie("UID02",null,1);
                setCookie("lastClickedButtonId",null, 1);
                location.href="login.html";
            });

            // 重設所有會員活動狀態
            $("#member_f5").click(function(){
                var dataJSON = {};
                dataJSON["Online"] = '0';
                $.ajax({
                    type: "POST",
                    url: "api/manager/manager-down-api.php",
                    data: JSON.stringify(dataJSON),
                    dataType: "json",
                    success: showdata_Down_member,
                    error: function () {
                        alert("error-api/manager/manager-down-api.php");
                    }
                });
                
            });

            //計算有在活動的會員人數
            $("#member_count").click(function(){                
                $.ajax({
                    type: "GET",
                    url: "api/manager/manager-count-api.php",
                    data: JSON.stringify(dataJSON),
                    dataType: "json",
                    success: showdata_Count_member,
                    error: function () {
                        alert("error-api/manager/manager-count-api.php");
                    }
                });
                
            });

            // 讀取會員資料
            $.ajax({
                type: "GET",
                url: "api/member/member-Read-api.php",
                async: false,
                dataType: "json",
                success: showdata,
                error: function () {
                    alert("error-api/member/member-Read-api.php");
                }
            });

            //$(" #update_member_btn").click(function(){}) 更新
            $("body").on("click" ," #member_data #update_member_btn", function () {                
                console.log($(this).data("userid") +','+ $(this).data("username") +','+ $(this).data("email")+','+ $(this).data("state"));
                u_id = $(this).data("id");
                $("#updateModal_userID").val($(this).data("userid"));
                $("#updateModal_userName").val($(this).data("username"));
                $("#updateModal_email").val($(this).data("email"));
                $("#updateModal_state").val($(this).data("state"));
            });

             //#member_Modal_updata_btn 監聽
             $("#member_Modal_updata_btn").click(function () {
                //傳遞更新資料至後端api {"UserID":"XX", "UserName":"XX", "Email":"XX", "State":"XX"}
                var dataJSON = {};
                dataJSON["ID"] = u_id;
                dataJSON["UserName"] = $("#updateModal_userName").val();
                dataJSON["Email"] = $("#updateModal_email").val();
                dataJSON["State"] = $("#updateModal_state").val();
                console.log(JSON.stringify(dataJSON));

                $.ajax({
                    type: "POST",
                    url: "api/member/member-Update-api.php",
                    async: false,
                    data: JSON.stringify(dataJSON),
                    dataType: "json",
                    success:  showdata_updata,
                            
                    error: function () {
                        alert("error-api/member/member-Update-api.php");
                    }
                });
            });

             
    });

        // 按鈕ID監聽
        $(document).ready(function() {
            // 從 cookie 中讀取上次點擊的按鈕 ID
            var lastClickedButtonId = getCookie("lastClickedButtonId");
        
            // 如果有存儲按鈕 ID，則自動觸發該按鈕的點擊事件
            if (lastClickedButtonId) {
                $("#" + lastClickedButtonId).click();
            }
        
            // 監聽按鈕點擊事件
            $('.myButton').click(function() {
                // 獲取按鈕的 ID
                var buttonId = $(this).attr('id');
        
                // 將按鈕的 ID 儲存在 cookie 中，有效期為 1 天
                setCookie("lastClickedButtonId", buttonId, 1);
            });
    });
    
        function showdata_Check_UID(data) {
                console.log(data);
                if (data.state) {
                    //驗證成功
                    $("#user_message").text("登入中!");

                }else{
                    location.replace("login.html");
                }
            }
        

        function showdata(data) {
        //整理資料儲存為二維陣列
        data.data.forEach(function (item, key) {
               console.log(key);
               if (key % 5 == 0) {
                    newData.push([]);
                }
                var page = parseInt(key / 5);
                newData[page].push(item);
            });
            drawTable(0);

            //產生頁碼
            $("#pageList_member").empty();
            newData.forEach(function(item, key){
                var thisPage = key + 1;
                var strHTML = '<li class="page-item"><a class="page-link" href="#" onclick="drawTable(' + key + ')">'+ thisPage +'</a></li>';
                $("#pageList_member").append(strHTML);
            });

            console.log(newData);
        }

        function drawTable(page) {
            
            $("#member_data").empty();
            newData[page].forEach(function (item) {
                var strHTML ='<tr><td>'+item.ID+'</td>'+ 
                        '<td>'+item.userID+'</td>'+                     
                        '<td>'+item.userName+'</td>'+                        
                        '<td>'+item.email+'</td>'+
                        '<td>'+item.state+'</td>'+
                        '<td>'+item.create_time+'</td><td>'+
            '<button data-ID="'+item.ID+'"data-userID="'+item.userID+'" data-userName="'+item.userName+'" data-email="'+item.email+'" data-state="'+item.state+'" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#UmemberModal" id="update_member_btn"">更新</button></td></tr>';

                $("#member_data").append(strHTML);
            });
           
            
        }

        function showdata_updata(data) {
            console.log(data); 
            if (data.state) {
                location.reload();
                    //驗證成功
                   alert("更新成功");
                   
                   
                   
                }      
        }

        function showdata_Down_member(data) {
            console.log(data);
            if (data.state) {                
                    //驗證成功
                   alert("更新成功");    
                }   
        }

        function showdata_Count_member(data) {
            console.log(data);
            if (data.state) {                
                    //驗證成功                
                  $("#member_online").text(data.data[0]['count(online)'])  ;
                }   
        }
        
      //w3s
      function setCookie(cname, cvalue, exdays) {

            
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        //w3s
        function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
        }
