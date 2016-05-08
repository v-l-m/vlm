 var _IsLoggedIn;
 


function CreateUser()
{
  var Player = {IdPlayer:0, IsAdmin:false, PlayerName:'', PlayerJID:'',
                Fleet : [],
                BSFleet: []
                };
  
  return Player;
};

function IsLoggedIn()
{
  return _IsLoggedIn;
};

// Show or hides login panel according to login state
function ShowLoginPanel()
{
  // Handle login panel
  if (IsLoggedIn())
  {
   $(".LoginPanel").hide();
   SetupUserMenu();
  }
  else
  {
    //var output = 'before ' + $(".LoginPanel:first").offsetleft + ' ' + $(".LoginPanel").offsettop + ' ' + $(".LoginPanel").width();
    //console.log( output );
    
    // Set position in center of screen
    var destx = $(document).width()/2 - $(".LoginPanel").width() /2 + 'px';
    var desty = $(document).height()/2 - $(".LoginPanel").height() /2 +'px';
    
    console.log( "dest " + destx + " " + desty );
    // Show Panel
    $(".LoginPanel").css({visibility: "visible"});
    
    $(".LoginPanel").show();
    $(".LoginPanel").animate({left: destx,
                              top: desty},0);
    
    $(".UserMenu").hide();
    }
};

function OnLoginRequest()
{
  
  var user = $(".UserName").val();
  var password = $(".UserPassword").val();
  
  $.ajaxSetup({username : user, password: password});
  
  $.post("/ws/login.php", 
          {VLM_AUTH_USER:user,
            VLM_AUTH_PW:password
          },
          function(result)
          {
            // :( calls login twice but avoid coding twice
            // Should use events to splits GUI from WS processing
            CheckLogin();
          }
        );
  
  ShowLoginPanel();
};

function CheckLogin()
{
  $.post("/ws/login.php", 
        function(result)
        {
          var LoginResult = JSON.parse(result);
          
          _IsLoggedIn= LoginResult.success==true;
          ShowLoginPanel();
              
          if (_IsLoggedIn)
          {
            GetPlayerInfo();
          }
        }
      );
  
  }

function Logout()
{
  $.get("/ws/logout.php",
        function(result)
        {
          var i = result;
          if (!result.success)
          {
            alert("Something bad happened while logging out. Restart browser...");
          }
        }
        );
  _IsLoggedIn=false;
  ShowLoginPanel();
}
  
function GetPlayerInfo()
 {
   $.get("/ws/playerinfo/profile.php",
          function(result)
          {
            if (result.success)
            {
              // Ok, create a user from profile
              _CurPlayer = CreateUser()
              _CurPlayer.IdPlayer = result.profile.idp;
              _CurPlayer.IsAdmin  = result.profile.admin;
              _CurPlayer.PlayerName  = result.profile.playername;
              
              RefreshPlayerMenu();
            }
            else
            {
              // Something's wrong, act as not logged in
              Logout();
              return;
            }
          }
        );
   $.get("/ws/playerinfo/fleet_private.php",
          function(result)
          {
            var i = result;
          }
        )
        
   
 }

function RefreshPlayerMenu()
{
  // Update GUI for current player
   $("#PlayerId").text(_CurPlayer.PlayerName);
   
}

function SetupUserMenu()
{
  // Set position in center of screen
  var destx = $(document).width()/2 - $(".UserMenu").width() /2 + 'px';
  var desty = 0;
  
  // Show Panel
  $(".UserMenu").show();
  $(".UserMenu").animate({left: destx,
                          top: desty},0);
    
}


