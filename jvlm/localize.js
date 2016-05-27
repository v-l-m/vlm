 var _LocaleDict;

function LocalizeString()
{
  //console.log("Localizing...");
  LocalizeItem($("[I18n]").get());
  
  // Handle flag clicks
  $(".LngFlag").click(
    function(event,ui)
    {
      OnLangFlagClick($(this).attr('lang'));
    }
  );
  
  return true;
}

function OnLangFlagClick(Lang)
{
  InitLocale(Lang)
}

function LocalizeItem( Elements )
{
  try
  {
    //console.log(Elements);
    for ( child in Elements )
    {
      var el = Elements[child];
      var Attr = el.attributes.I18n.value;
      
      if (typeof _LocaleDict != "undefined")
      {
        if (Attr in _LocaleDict)
        {
          el.innerHTML = _LocaleDict[Attr];
        }
      }      
    }

  } 
  finally
  {
  }    
  return true
}

function InitLocale(Lang)
{
  var query = "/ws/serverinfo/translation.php"
  
  if (Lang)
  {
    query += "?lang=" + Lang;
  }
  $.get( query,
          function(result)
          {
            if (result.success == true)
            {
              _LocaleDict=result.strings;
              LocalizeString();
            }
            else
            {
              alert("Localization string table load failure....");
            }
          }
         );
}