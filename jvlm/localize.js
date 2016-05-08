 var _LocaleDict;

function LocalizeString()
{
  console.log("Localizing...");
  LocalizeItem($("[I18n]").get());
  return true;
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
      
      if (Attr in _LocaleDict)
      {
        el.innerHTML = _LocaleDict[Attr];
      }
      
      //LocalizeItem(child);
    }

  } 
  finally
  {
  }    
  return true
}

function InitLocale()
{
  $.get("/ws/serverinfo/translation.php",
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