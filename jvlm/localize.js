 var _LocaleDict;
 var _EnDict;
 var _CurLocale = 'en'; // Default to english unless otherwise posted
 var _SyncLocalizedCallBack = [];

 function LocalizeString()
 {
   //console.log("Localizing...");
   LocalizeItem($("[I18n]").get());

   // Handle flag clicks
   $(".LngFlag").click(
     function(event, ui)
     {
       let lang = $(this).attr('lang');

       if (!lang)
       {
         //let Label = e.currentTarget;
         let img = $(this).siblings("img");
         if (img)
         {
           lang = img[0].attributes.lang.value;
         }

       }
       if (lang)
       {
         OnLangFlagClick(lang);
         UpdateLngDropDown();
       }
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

 // IE polyfill for includes function
 // from https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/includes#Polyfill
 if (!String.prototype.includes)
 {
   String.prototype.includes = function(search, start)
   {
     'use strict';

     if (search instanceof RegExp)
     {
       throw TypeError('first argument must not be a RegExp');
     }
     if (start === undefined)
     {
       start = 0;
     }
     return this.indexOf(search, start) !== -1;
   };
 }

 function InitLocale(Lang)
 {
   var query = "/ws/serverinfo/translation.php";

   if (Lang && typeof Lang === "string")
   {
     if (Lang.includes("-"))
     {
       Lang = Lang.split("-")[0];
     }
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

         for (let index in _SyncLocalizedCallBack)
         {
           if (_SyncLocalizedCallBack[index])
           {
             _SyncLocalizedCallBack[index]();
           }
         }
         _SyncLocalizedCallBack = [];

         let Str = "";

         for (let i = 0; i < 24; i += 6)
         {
           if (Str !== "")
           {
             Str += ", ";
           }

           let m = moment.utc().minutes(30).hour(i + 3);
           if (VLM2Prefs && VLM2Prefs.MapPrefs && VLM2Prefs.MapPrefs.UseUTC)
           {
             Str += m.format("LT");
           }
           else
           {
             Str += m.local().format("LT");
           }
         }
         if (VLM2Prefs && VLM2Prefs.MapPrefs && VLM2Prefs.MapPrefs.UseUTC)
         {
           Str += " UTC";
         }
         else
         {
           Str += " " + GetLocalizedString('LocalTime');
         }

         $(".AProposLine").html(GetLocalizedString("a1", Str));
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
   // Get rid of %25 (redirect side effect)
   while (String.includes("%25"))
   {
     String = String.replace("%25", "%");
   }
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

 function WaitLocaleInited(callback)
 {
   if (!callback)
   {
     return false;
   }

   if (_LocaleDict || _EnDict)
   {
     return true;
   }
   else
   {
     _SyncLocalizedCallBack.push(callback);
   }

   return false;

 }