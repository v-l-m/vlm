 var _IsLoggedIn;
 
function Boat()
{
  this.IdBoat=-1;
  this.Engaged=false;
  this.BoatName='';
  this.BoatPseudo='';
  this.VLMInfo={};  // LastBoatInfoResult
  
}

function Boat(vlmboat)
{
  this.IdBoat=vlmboat.idu;
  this.Engaged=vlmboat.engaged;
  this.BoatName=vlmboat.boatname;
  this.BoatPseudo=vlmboat.boatpseudo;
}


function User()
{
  this.IdPlayer=-1;
  this.IsAdmin=false;
  this.PlayerName='';
  this.PlayerJID='';
  this.Fleet = [];
  this.BSFleet= [];
};

function IsLoggedIn()
{
  return _IsLoggedIn;
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
  
};

function CheckLogin()
{
  ShowPb("#PbLoginProgress");
  $.post("/ws/login.php", 
        function(result)
        {
          var LoginResult = JSON.parse(result);
          
          _IsLoggedIn= LoginResult.success==true;
              
          if (_IsLoggedIn)
          {
            GetPlayerInfo();
          }
          HidePb("#PbLoginProgress");
        }
      );
  
  }

function Logout()
{
  DisplayLoggedInMenus(false);
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
  
}
  
function GetPlayerInfo()
 {
   ShowBgLoad();
   $.get("/ws/playerinfo/profile.php",
          function(result)
          {
            if (result.success)
            {
              // Ok, create a user from profile
              if ( typeof _CurPlayer == 'undefined' )
              {
                _CurPlayer = new User();
              }
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
            var select
            
            if (typeof _CurPlayer == 'undefined')
              {
                _CurPlayer = new User();
              }
            for (boat in result.fleet)
            {  
              _CurPlayer.Fleet.push (new Boat(result.fleet[boat]));
              if ( typeof select == "undefined")
              {
                //SetCurrentBoat(GetBoatFromIdu(boat));
                select = boat;
              }
            }
            for (boat in result.fleet_boatsit)
            {  
              _CurPlayer.BSFleet.push (new Boat(result.fleet_boatsit[boat]));
            }
            
            RefreshPlayerMenu();
          }
        )
        
   
 }

function RefreshPlayerMenu()
{
  
  
  // Update GUI for current player
  $("#PlayerId").text(_CurPlayer.PlayerName);
   
  // Update the combo to select the current boat
  ClearBoatSelector();
  for (boat in _CurPlayer.Fleet)
  {
    AddBoatToSelector(_CurPlayer.Fleet[boat],true);
  }
  for (boat in _CurPlayer.BSFleet)
  {
    AddBoatToSelector(_CurPlayer.BSFleet[boat],false);
  }
  
  ShowUserBoatSelector();
  $('#BoatSelector').prop("selectedIndex",0);
  $('#BoatSelector').selectmenu('refresh');
            //$('#BoatSelector').val(boat);
            //$('#BoatSelector').selectmenu("refresh");
            
  
  DisplayLoggedInMenus(true);
  HideBgLoad();
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

function GetBoatFromIdu(Id)
{
  var RetBoat= GetBoatFromBoatArray(_CurPlayer.Fleet,Id);
  
  if (typeof RetBoat == 'undefined')
  {
    RetBoat= GetBoatFromBoatArray(_CurPlayer.BSFleet,Id);
  }
  
  return RetBoat;
 }

function GetBoatFromBoatArray(BoatsArray, Id)
{
  for (boat in BoatsArray)
  {
    if (BoatsArray[boat].IdBoat == Id)
    {
      return BoatsArray[boat];
    }
  }
  return ;
}
