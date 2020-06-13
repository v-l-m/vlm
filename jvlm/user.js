 var _IsLoggedIn;


 function Boat(vlmboat)
 {
   // Default init
   this.IdBoat = function()
   {
     if (this.VLMInfo)
     {
       if (typeof this.VLMInfo.IDU === "string")
       {
         this.VLMInfo.IDU = parseInt(this.VLMInfo.IDU, 10);
       }
       return this.VLMInfo.IDU;
     }
     else
     {
       return null;
     }
   };

   this.Engaged = function()
   {
     if (this.VLMInfo)
     {
       if (typeof this.VLMInfo.engaged === "undefined" && this.VLMInfo.RAC)
       {
         this.VLMInfo.engaged = parseInt(this.VLMInfo.RAC, 10);
       }
       return this.VLMInfo.engaged;
     }
     else
     {
       return 0;
     }
   };

   this.BoatName = function()
   {
     if (this.VLMInfo)
     {
       if (!this.VLMInfo.boatname && this.VLMInfo.IDB)
       {
         this.VLMInfo.boatname = this.VLMInfo.IDB;
       }
       return this.VLMInfo.boatname;
     }
     else
     {
       return null;
     }
   };

   this.BoatPseudo = function()
   {
     if (this.VLMInfo)
     {
       return this.VLMInfo.boatpseudo;
     }
     else
     {
       return null;
     }
   };

   this.VLMInfo = {}; // LastBoatInfoResult
   this.RaceInfo = {}; // Race Info for the boat
   this.Exclusions = []; // Exclusions Zones for this boat
   this.Track = []; // Last 24H of boat Track
   this.RnkObject = {}; // Ranking table
   this.OppTrack = []; // Opponents tracks table
   this.OppList = []; // Opponents list to limit how many boats are shown
   this.Reals = []; // Reals Boat array
   this.VLMPrefs = []; // Preferences Array;
   this.NextServerRequestDate = null; // Next VAC Start date
   this.Estimator = new Estimator(this); // Estimator object for current boat
   this.EstimatePos = null; // Position marker on estimate track

   if (typeof vlmboat !== 'undefined')
   {
     this.VLMInfo.IDU = vlmboat.idu;
     this.VLMInfo.boatname = vlmboat.boatname;
     this.VLMInfo.boatpseudo = vlmboat.boatpseudo;
     if (vlmboat.engaged || vlmboat.engaged === 0)
     {
       this.VLMInfo.engaged = vlmboat.engaged;
     }
     else if (vlmboat.RAC)
     {
       this.VLMInfo.engaged = parseInt(vlmboat.RAC, 10);
     }
     this.RaceInfo = vlmboat.RaceInfo;
     this.Exclusions = vlmboat.Exclusions;
     this.Track = vlmboat.Track;
     this.RnkObject = vlmboat.RnkObject;
   }

   this.GetNextGateSegment = function(NWP)
   {

     if (typeof NWP === "string")
     {
       NWP = parseInt(NWP, 10);
     }

     if (typeof this.RaceInfo === "undefined")
     {
       return null;
     }

     NormalizeRaceInfo(this.RaceInfo);
     let Gate = this.RaceInfo.races_waypoints[NWP];

     // Loop to look for next racing gate. ICE_Gate are not used to AutoWP routing
     do {
       // Make sure gate type is handled as a number
       if (typeof Gate === "string")
       {
         Gate = parseInt(Gate, 10);
       }

       if (Gate.wpformat & WP_ICE_GATE)
       {
         NWP++;
         if (NWP >= this.RaceInfo.races_waypoints)
         {
           throw "Oops could not find requested gate type";
         }
         Gate = this.RaceInfo.races_waypoints[NWP];
       }

     } while (Gate.wpformat & WP_ICE_GATE);

     let P1 = new VLMPosition(Gate.longitude1, Gate.latitude1);
     let P2 = {};
     if ((Gate.wpformat & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS || (Gate.longitude2 && Gate.latitude2))
     {
       P2 = new VLMPosition(Gate.longitude2, Gate.latitude2);
     }
     else
     {
       // No Second buoy, compute segment end
       let Dest = Compute2ndBuoyOfGate(Gate);
       P2 = new VLMPosition(Dest.Lon.Value, Dest.Lat.Value);
     }

     return {
       P1: P1,
       P2: P2
     };


   };

   this.GetClosestEstimatePoint = function(Pos)
   {
     if (typeof Pos === "undefined" || !Pos)
     {
       if (this.Estimator)
       {
         this.Estimator.ClearEstimatePosition(this.Estimator.Boat);
       }
       return null;
     }

     if (this.Estimator)
     {
       var Est = this.Estimator.GetClosestEstimatePoint(Pos);
       if (Est)
       {
         this.Estimator.ShowEstimatePosition(this.Estimator.Boat, Est);
       }
       else
       {
         this.Estimator.ClearEstimatePosition(this.Estimator.Boat);
       }
       return Est;
     }
     else
     {
       this.Estimator.ShowEstimatePosition(null, null);
       return null;
     }
   };


   this.GetNextWPPosition = function(NWP, Position, NWPPosition)
   {

     if (typeof this.VLMInfo === "undefined")
     {
       // Should not come here without some kind of VLMInfo...
       return null;
     }

     // Assume if we get there, there is a boat with RaceInfo and VLMInfo loaded
     var WPIndex = this.VLMInfo.NWP;

     //If there is a defined WP, then return it
     if ((typeof NWPPosition === "undefined" || (!NWPPosition)) && ((this.VLMInfo.WPLON !== "0") || (this.VLMInfo.WPLAT !== "0")))
     {
       return new VLMPosition(this.VLMInfo.WPLON, this.VLMInfo.WPLAT);
     }
     else if (typeof NWPPosition !== "undefined" && NWPPosition && NWPPosition.Lon.Value !== 0 && NWPPosition.Lat.Value !== 0)
     {
       return new VLMPosition(NWPPosition.Lon.Value, NWPPosition.Lat.Value);
     }
     else
     {
       // Get CurRaceWP
       // Compute closest point (using bad euclidian method)
       // Return computed point

       var CurWP = this.VLMInfo.NWP;
       if (typeof NWP !== "undefined" && NWP)
       {
         CurWP = NWP;
       }
       var Seg = this.GetNextGateSegment(CurWP);

       if (typeof Seg === "undefined" || (!Seg))
       {
         return null;
       }
       var Loxo1 = Seg.P1.GetLoxoCourse(Seg.P2);
       var CurPos;
       if (typeof Position !== "undefined" && Position)
       {
         CurPos = Position;
       }
       else
       {
         CurPos = new VLMPosition(this.VLMInfo.LON, this.VLMInfo.LAT);
       }
       var Loxo2 = Seg.P1.GetOrthoCourse(CurPos);
       var Delta = Loxo1 - Loxo2;

       if (Delta > 180)
       {
         Delta -= 360.0;
       }
       else if (Delta < -180)
       {
         Delta += 360.0;
       }

       Delta = Math.abs(Delta);

       if (Delta > 90)
       {
         return Seg.P1;
       }
       else
       {
         var PointDist = Seg.P1.GetLoxoDist(CurPos);
         try
         {
           var SegDist = PointDist * Math.cos(Deg2Rad(Delta));
           var SegLength = Seg.P1.GetLoxoDist(Seg.P2);
           if (SegLength > SegDist)
           {
             return Seg.P1.ReachDistLoxo(SegDist, Loxo1);
           }
           else
           {
             return Seg.P2;
           }
         }
         catch (e)
         {
           return null;
         }

       }
     }


   };
 }


 class User
 {
   constructor()
   {
     this.MaxFleetSize = 12;
     this.IdPlayer = -1;
     this.IsAdmin = false;
     this.PlayerName = '';
     this.PlayerJID = '';
     this.Fleet = [];
     this.BSFleet = [];
     this.CurBoat = {};

     this.LastLogin = 0;

     this.KeepAlive = function()
     {
       console.log("Keeping login alive...");
       CheckLogin();
     };

     // Send Login every 10'
     setInterval(this.KeepAlive, 600000);
   }
 }

 var _CurPlayer = new User();

 function IsLoggedIn()
 {
   if (_IsLoggedIn)
   {
     StatMGR.CheckGConsent();
   }
   return _IsLoggedIn;
 }


 function OnLoginRequest()
 {


   CheckLogin(true);

 }

 function GetPHPSessId()
 {
   let Session = document.cookie.split(";");
   let index;

   for (index in Session)
   {
     if (Session[index])
     {
       let f = Session[index].split("=");

       if (f[0] && f[0].trim() === "PHPSESSID")
       {
         return f[0];
       }
     }
   }

   return null;

 }

 function CheckLogin(GuiRequest)
 {
   let user = $(".UserName").val();
   let password = $(".UserPassword").val();
   let PhpSessId = GetPHPSessId();

   if (PhpSessId || (typeof user === "string" && typeof password === "string" && user.trim().length > 0 && password.trim().length > 0))
   {
     ShowPb("#PbLoginProgress");
     $.post("/ws/login.php",
       {
         VLM_AUTH_USER: user.trim(),
         VLM_AUTH_PW: password.trim()
       },
       function(result)
       {
         let LoginResult = JSON.parse(result);
         let CurLoginStatus = _IsLoggedIn;
         let CurBoatID = null;


         if (CurLoginStatus)
         {
           CurBoatID = _CurPlayer.CurBoatID;
         }

         _IsLoggedIn = LoginResult.success === true;

         HandleCheckLoginResponse(GuiRequest);

         if (CurBoatID)
         {
           SetCurrentBoat(GetBoatFromIdu(CurBoatID), false);
         }
       }
     );
   }
   else
   {
     HandleCheckLoginResponse(GuiRequest);
   }

 }

 function HandleCheckLoginResponse(GuiRequest)
 {
   if (_IsLoggedIn)
   {
     StatMGR.CheckGConsent();
     GetPlayerInfo();
   }
   else if (GuiRequest)
   {
     VLMAlertDanger(GetLocalizedString("authfailed"));
     $(".UserPassword").val("");
     // Reopened login dialog
     setTimeout(function()
     {
       $("#LoginForm").modal("hide").modal("show");
     }, 1000);
     initrecaptcha(true, false);
     $("#ResetPasswordLink").removeClass("hidden");

   }
   HidePb("#PbLoginProgress");
   DisplayLoggedInMenus(_IsLoggedIn);

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
         VLMAlertDanger("Something bad happened while logging out. Restart browser...");
         windows.location.reload();
       }
       else
       {
         window.location.reload();
       }
     }
   );
   _IsLoggedIn = false;

 }

 function GetPlayerInfo(ShowIdu)
 {
   ShowBgLoad();
   $.get("/ws/playerinfo/profile.php",
     function(result)
     {
       if (result.success)
       {
         // Ok, create a user from profile
         if (typeof _CurPlayer === 'undefined' || !_CurPlayer)
         {
           _CurPlayer = new User();
         }
         _CurPlayer.IdPlayer = result.profile.idp;
         _CurPlayer.IsAdmin = result.profile.admin;
         _CurPlayer.PlayerName = result.profile.playername;

         $.get("/ws/playerinfo/fleet_private.php", function(e, ShowIdu)
         {
           HandleFleetInfoLoaded(e, ShowIdu);
         });
       }
       else
       {
         // Something's wrong, act as not logged in
         Logout();
         return;
       }
     }
   );


 }

 function HandleFleetInfoLoaded(result, ShowIdu)
 {
   let i = result;
   let select = null;

   if (typeof _CurPlayer === 'undefined')
   {
     _CurPlayer = new User();
   }

   if (typeof _CurPlayer.Fleet === "undefined")
   {
     _CurPlayer.Fleet = [];
   }

   _CurPlayer.FleetSize = 0;
   for (let boat in result.fleet)
   {
     if (typeof _CurPlayer.Fleet[boat] === "undefined")
     {
       _CurPlayer.Fleet[boat] = (new Boat(result.fleet[boat]));
       if (ShowIdu === boat || !select || (select && !select.Engaged() && _CurPlayer.Fleet[boat].Engaged()))
       {
         select = _CurPlayer.Fleet[boat];
       }
     }
     _CurPlayer.FleetSize += 1;
   }

   $("#FleetSizeInfo").text(" (" + _CurPlayer.FleetSize + "/" + _CurPlayer.MaxFleetSize + ')');

   if (typeof _CurPlayer.fleet_boatsit === "undefined")
   {
     _CurPlayer.fleet_boatsit = [];
   }

   for (let boat in result.fleet_boatsit)
   {
     if (typeof _CurPlayer.BSFleet[boat] === "undefined")
     {
       _CurPlayer.BSFleet[boat] = (new Boat(result.fleet_boatsit[boat]));
     }
   }

   RefreshPlayerMenu();
   if (typeof select !== "undefined" && select)
   {
     DisplayCurrentDDSelectedBoat(select);
     SetCurrentBoat(select, true);
     RefreshCurrentBoat(true, false);
   }
 }

 function RefreshPlayerMenu()
 {
   // Update GUI for current player
   $("#PlayerId").text(_CurPlayer.PlayerName);

   // Update the combo to select the current boat
   ClearBoatSelector();
   for (let boat in _CurPlayer.Fleet)
   {
     AddBoatToSelector(_CurPlayer.Fleet[boat], true);
   }
   for (let boat in _CurPlayer.BSFleet)
   {
     if (_CurPlayer.BSFleet[boat])
     {
       AddBoatToSelector(_CurPlayer.BSFleet[boat], false);
     }
   }
   UpdateBoatList();
   DisplayLoggedInMenus(true);
   HideBgLoad("#PbLoginProgress");
 }

 function SetupUserMenu()
 {
   // Set position in center of screen
   var destx = $(document).width() / 2 - $(".UserMenu").width() / 2 + 'px';
   var desty = 0;

   // Show Panel
   $(".UserMenu").show();
   $(".UserMenu").animate(
   {
     left: destx,
     top: desty
   }, 0);

 }

 function GetBoatFromIdu(Id)
 {
   if (typeof _CurPlayer === "undefined")
   {
     return;
   }
   var RetBoat = GetBoatFromBoatArray(_CurPlayer.Fleet, Id);

   if (typeof RetBoat === 'undefined')
   {
     RetBoat = GetBoatFromBoatArray(_CurPlayer.BSFleet, Id);
   }

   return RetBoat;
 }

 function GetBoatFromBoatArray(BoatsArray, Id)
 {
   Id = parseInt(Id, 10);

   for (let boat in BoatsArray)
   {
     if (BoatsArray[boat] && (BoatsArray[boat].IdBoat() === Id))
     {
       return BoatsArray[boat];
     }
   }
   return;
 }

 function GetFlagsList()
 {
   $.get("/ws/serverinfo/flags.php",
     function(result)
     {
       let i = result;
       if (result.success)
       {
         var DropDown = $("#CountryDropDownList");
         var flagindex = 0;
         for (let index in result.flags)
         {
           if (result.flags[index])
           {
             let title = result.flags[index];
             DropDown.append("<li class='FlagLine DDLine' flag='" + title + "'>" + GetCountryDropDownSelectorHTML(title, true, flagindex++) + "</li>");
           }
         }
       }

       // Catch flag selection change
       $(".FlagLine").on('click', HandleFlagLineClick);

     }
   );
 }

 var FlagsIndexCache = [];

 function GetCountryDropDownSelectorHTML(title, loadflag, CountryIndex)
 {
   if (loadflag)
   {
     // Get line to build DropDown
     //var RetString1 = " <img class='flag' src='/cache/flags/flagsmap.png' flag='"+title+"' title='"+title+"' alt='"+title+"'></img>"
     let RetString1 = GetCountryFlagImg(title, CountryIndex);
     let RetString2 = " <span  class='FlagLabel' flag='" + title + "'> - " + title + "</span>";

     FlagsIndexCache[title] = RetString1;
   }
   let RetString2 = " <span  class='FlagLabel' flag='" + title + "'> - " + title + "</span>";
   return FlagsIndexCache[title] + RetString2;
 }

 function GetCountryFlagImgHTML(country)
 {
   return FlagsIndexCache[country];
 }

 function GetCountryFlagImg(Title, CountryIndex)
 {
   var row = 20 * Math.floor(CountryIndex / 16);
   var col = 30 * (CountryIndex % 16);
   var RetString1 = " <div class='FlagIcon' style='background-position: -" + col + "px -" + row + "px' flag='" + Title + "'></div>";

   return RetString1;
 }