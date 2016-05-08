 var _IsLoggedIn;
 
// On ready get started with vlm management
// This should be somewhere else...
$(document).ready(
  function(){
    // Load translation strings
    InitLocale()
    
    // Init event handlers
    // Login button click event handler
    $(".LoginButton").click( 
      function()
      {
        OnLoginRequest();
      }
    );   

    // Create user object
    CreateUser();
    
    // CheckLogin
    CheckLogin();
     
  }  
);

function CreateUser()
{
  
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
    $(".LoginPanel").show();
    $(".LoginPanel").animate({left: destx,
                              top: desty},0);
    
    $(".LoginPanel").show();
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
            var LoginResult = JSON.parse(result);
            
            _IsLoggedIn= LoginResult.success==true;
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
  
        }
      );
  
  }





