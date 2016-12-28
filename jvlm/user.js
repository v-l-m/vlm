 var _IsLoggedIn;


function Boat(vlmboat)
{
// Default init
  this.IdBoat=-1;
  this.Engaged=false;
  this.BoatName='';
  this.BoatPseudo='';
  this.VLMInfo = {};  // LastBoatInfoResult
  this.RaceInfo = {}; // Race Info for the boat
  this.Exclusions = []; // Exclusions Zones for this boat
  this.Track = []; // Last 24H of boat Track
  this.Rankings = {};   // Ranking table
  this.OppTrack = []; // Opponents tracks table
  this.OppList = [];  // Opponents list to limit how many boats are shown
  this.Reals = []; // Reals Boat array
  this.VLMPrefs = []; // Preferences Array;
  this.NextServerRequestDate;  // Next VAC Start date
  this.Estimator = new Estimator(this) ; // Estimator object for current boat

  if (typeof vlmboat != 'undefined')
  {
    this.IdBoat=vlmboat.idu;
    this.Engaged=vlmboat.engaged;
    this.BoatName=vlmboat.boatname;
    this.BoatPseudo=vlmboat.boatpseudo;
    this.VLMInfo=vlmboat.VLMInfo;
    this.RaceInfo=vlmboat.RaceInfo; 
    this.Exclusions=vlmboat.Exclusions; 
    this.Track=vlmboat.Track; 
    this.Rankings=vlmboat.Rankings;  
  }

  this.GetNextWPPosition= function()
  {
    // Assume if we get there, there is a boat with RaceInfo and VLMInfo loaded
    var WPIndex = this.VLMInfo.NWP;

    //If there is a defined WP, then return it
    if ((this.VLMInfo.WPLON!=0)||(this.VLMInfo.WPLAT!=0))
    {
      return new VLMPosition (this.VLMInfo.WPLON,this.VLMInfo.WPLAT);
    }
    else
    {
      // Use boat ortho and distance to compute default WP
      var P = new VLMPosition(this.VLMInfo.LON,this.VLMInfo.LAT)
      return P.ReachDistOrtho(this.VLMInfo.DNM,this.VLMInfo.ORT)
    }
    

  }
}


function User()
{
  this.IdPlayer=-1;
  this.IsAdmin=false;
  this.PlayerName='';
  this.PlayerJID='';
  this.Fleet = [];
  this.BSFleet= [];
  this.CurBoat={};  
  
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
          DisplayLoggedInMenus(_IsLoggedIn);
  
        }
      );
  
  }

function Logout()
{
  DisplayLoggedInMenus(false);
  $.post("/ws/logout.php",
        function(result)
        {
          var i = result;
          if (!result.success)
          {
            alert("Something bad happened while logging out. Restart browser...");
            windows.location.reload();
          }
          else
          {
            window.location.reload();
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
            
            if (typeof _CurPlayer === 'undefined')
            {
              _CurPlayer = new User();
            }

            _CurPlayer.Fleet = [];
            for (boat in result.fleet)
            {  
              _CurPlayer.Fleet[boat]= (new Boat(result.fleet[boat]));
              if ( typeof select == "undefined")
              {
                select = _CurPlayer.Fleet[boat];
              }
            }

            
            _CurPlayer.fleet_boatsit = [];
            for (boat in result.fleet_boatsit)
            {  
              _CurPlayer.BSFleet.push (new Boat(result.fleet_boatsit[boat]));
            }
            
            RefreshPlayerMenu();
            DisplayCurrentDDSelectedBoat(select);
            SetCurrentBoat(GetBoatFromIdu(select),true);                
            RefreshCurrentBoat (true,false)    
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
  
  DisplayLoggedInMenus(true);
  HideBgLoad("#PbLoginProgress");
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
  if (typeof _CurPlayer === "undefined")
  {
    return;
  }
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

function GetFlagsList()
{
  $.get("/ws/serverinfo/flags.php",
        function(result)
        {
          var i = result;
          if (result.success)
          {
            var DropDown=$("#CountryDropDownList");

            for (index in result.flags)
            {
              var title = result.flags[index];
              DropDown.append("<li class='FlagLine DDLine' flag='"+ title +"'>"+GetCountryDropDownSelectorHTML(title,true)+"</li>")
            }
          }

          // Catch flag selection change
          $(".FlagLine").on('click',HandleFlagLineClick);

        }
        );
}

function GetCountryDropDownSelectorHTML(title,loadflag)
{
  var RetString1 = " <img class='flag' src='/cache/flags/"+encodeURIComponent( title)+".png' flag='"+title+"' title='"+title+"' alt='"+title+"'></img>"
  var RetString2 = " <span  style='margin-left:10px;' flag='"+title+"'> - "+ title +"</span>";

  if (loadflag)
  {
    return RetString1+RetString2
  }
  else
  {
    return RetString2
  }
}