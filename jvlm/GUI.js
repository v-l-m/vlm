// On ready get started with vlm management
// This should be somewhere else...
$(document).ready(
  function(){
    // Load translation strings
    InitLocale();
    
    // Init Menus()
    InitMenus();
    
    // Init event handlers
    // Login button click event handler
    $(".LoginButton").click( 
      function()
      {
        OnLoginRequest();
      }
    );   
    
    // Show logout menu on drop icon click
    $("#DropLogoutMenu").click(
      function()
      {
        // Show logout menu
        $("#Menu").toggle();
      }
    );
    
    // Handle menu selection
    $("#Menu").menu({select:
                        function (event, ui)
                        {
                          // Get localization key to figure out action
                          switch (ui.item.attr("I18n"))
                          {
                            case "logout":
                              $("#Menu").toggle();
                              Logout();
                              break;
                          }
                          
                        }
                    }
    );
    
    // CheckLogin
    CheckLogin();
     
  }  
);

function InitMenus()
{
  $( "#Menu" ).menu();
  $( "#Menu" ).hide();
  
}