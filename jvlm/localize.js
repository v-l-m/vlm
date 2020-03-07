 var _LocaleDict;
 var _EnDict;
 var _CurLocale = 'en'; // Default to english unless otherwise posted

 function LocalizeString()
 {
   //console.log("Localizing...");
   LocalizeItem($("[I18n]").get());

   // Handle flag clicks
   $(".LngFlag").click(
     function(event, ui)
     {
       OnLangFlagClick($(this).attr('lang'));
       UpdateLngDropDown();
     }
   );

   return true;
 }

 function OnLangFlagClick(Lang)
 {
   InitLocale(Lang);
 }

 function LocalizeItem(Elements)
 {
   try
   {
     let child;

     //console.log(Elements);
     for (child in Elements)
     {
       let el = Elements[child];

       if (el && el.attributes && el.attributes.I18n)
       {
         let Attr = el.attributes.I18n.value;

         if (typeof _LocaleDict != "undefined")
         {
           el.innerHTML = GetLocalizedString(Attr);
         }
       }
     }

   }
   finally
   {}
   return true;
 }

 function InitLocale(Lang)
 {
   var query = "/ws/serverinfo/translation.php";

   if (Lang)
   {
     query += "?lang=" + Lang;
   }
   $.get(query,
     function(result)
     {
       if (result.success == true)
       {
         _CurLocale = result.request.lang;
         _LocaleDict = result.strings;
         moment.locale(_CurLocale);
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
     $.get("/ws/serverinfo/translation.php?lang=en",
       function(result)
       {
         if (result.success == true)
         {
           _EnDict = result.strings;
         }
         else
         {
           alert("Fallback localization string table load failure....");
         }
       }
     );
   }
 }

 function HTMLDecode(String)
 {
   let txt = document.createElement("textarea");
   txt.innerHTML = String;
   let RetString = txt.value;
   let EOLSigns = ["\n\r", "\r\n", "\n", "\r"];

   for (let index in EOLSigns)
   {
     while (EOLSigns[index] && RetString.indexOf(EOLSigns[index]) !== -1)
     {
       RetString = RetString.replace(EOLSigns[index], "<br>");
     }
   }

   return RetString;
 }

 function GetLocalizedString(StringId, params)
 {
   let RetString = "";

   if (typeof _LocaleDict !== "undefined" && _LocaleDict && StringId in _LocaleDict)
   {
     RetString = HTMLDecode(_LocaleDict[StringId]);
   }
   else if ((typeof _EnDict !== "undefined") && (_EnDict) && (StringId in _EnDict))
   {
     RetString = HTMLDecode(_EnDict[StringId]);
   }
   else
   {
     RetString = StringId;
   }

   if (params)
   {
     RetString = vsprintf(RetString, params);
   }

   return RetString;
 }

 function GetCurrentLocale()
 {
   return _CurLocale;
 }