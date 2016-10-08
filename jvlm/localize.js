 var _LocaleDict;
 var _EnDict;
 var _CurLocale= 'en';  // Default to english unless otherwise posted

function LocalizeString()
{
  //console.log("Localizing...");
  LocalizeItem($("[I18n]").get());
  
  // Handle flag clicks
  $(".LngFlag").click(
    function(event,ui)
    {
      OnLangFlagClick($(this).attr('lang'));
      UpdateLngDropDown();
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
    var child;
    
    //console.log(Elements);
    for ( child in Elements )
    {
      var el = Elements[child];
      var Attr = el.attributes.I18n.value;
      
      if (typeof _LocaleDict != "undefined")
      {
        el.innerHTML=GetLocalizedString(Attr);
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
              _CurLocale = result.request.lang;
              _LocaleDict=result.strings;
              LocalizeString();
              UpdateLngDropDown();
            }
            else
            {
              alert("Localization string table load failure....");
            }
          }
         );

	if (typeof _EnDict == 'undefined')
  {
    // Load english dictionnary as fall back on 1st call
    $.get( "/ws/serverinfo/translation.php?lang=en",
          function(result)
          {
            if (result.success == true)
            {
              _EnDict=result.strings;
            }
            else
            {
              alert("Fallback localization string table load failure....");
            }
          }
         );
  }  
}

function GetLocalizedString(StringId)
{
  if (StringId in _LocaleDict)
  {
    return _LocaleDict[StringId];
  }
  else if (StringId in _EnDict)
  {
    return _EnDict[StringId];
  }
  else
  {
    return StringId
  }
}

function GetCurrentLocale()
{
  return _CurLocale;
}