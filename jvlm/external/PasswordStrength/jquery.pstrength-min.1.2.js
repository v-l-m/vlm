(function(A)
  {A.extend(A.fn,
    {
      pstrength:function(B)
        {
          var B=A.extend(
              {
                verdects:["PwdForbiddenPass","PwdTooShort","PwdInvalidChars","PwdVeryweak","PwdWeak","PwdMedium","PwdStrong","PwdVerystrong"],
                colors:["#f00","#f00","#f00","#f00","#c06","#f60","#3c0","#3f0"],
                scores:[-200,-100,-50,10,15,30,40],
                pcts:[0,5,0,10,25,50,75,92],
                common:["password","sex","god","123456","123","liverpool","letmein","qwerty","monkey"],
                minchar:4},B);
                return this.each(function()
                                    {
                                      var C=A(this).attr("id");
                                      A(this).after("<div class=\"pstrength-minchar\" id=\""+C+"_minchar\">Minimum number of characters is "+B.minchar+"</div>");
                                      A(this).after("<div class=\"pstrength-info\" id=\""+C+"_text\"></div>");
                                      A(this).after("<div class=\"pstrength-bar\" id=\""+C+"_bar\" style=\"border: 1px solid white; font-size: 1px; height: 5px; width: 0px;\"></div>");
                                      A(this).keyup(function()
                                          {
                                            A.fn.runPassword(A(this).val(),C,B)
                                          })
                                    })
        },
      SetPwdClass:function(A,F,C,Class, Pct)
            {
              let B="#"+F+"_bar";
              let E="#"+F+"_text";
              strColor=C.colors[Class];
              strText=GetLocalizedString(C.verdects[Class]);
              A(B).css({width:Pct+"%"})
              A(B).css({backgroundColor:strColor});
              A(E).html("<span style='color: "+strColor+";'>"+strText+"</span>")
            },
      runPassword:function(D,F,C)
            {
              nPerc=A.fn.checkPassword(D,C);
              
              let index;

              for (index in C.scores)
              {
                if(nPerc<=C.scores[index])
                {
                  A.fn.SetPwdClass(A,F,C,index,C.pcts[index]);
                  break;
                }
                
              }
            },
      checkPassword:function(C,B)
            {
              let F=0;
              var E=B.verdects[0];
              if(C.length<B.minchar)
              {
                F=-100
                return F;
              }
              else if(C.length>=B.minchar&&C.length<=(B.minchar+2))
              {
                F=(F+6)
              }
              else if(C.length>=(B.minchar+3)&&C.length<=(B.minchar+4))
              {
                F=(F+12)
              }
              else if(C.length>=(B.minchar+5))
              {
                F=(F+18)
              }
              
              if(C.match(/[a-z]/))
              {
                F=(F+1)
              }
              if(C.match(/[A-Z]/))
              {
                F=(F+5)
              }
              if(C.match(/\d+/))
              {
                F=(F+5)
              }
              if (C.match(/[^a-zA-Z0-9]+/))
              {
                F=-50;
                return F;
              }
              if(C.match(/(.*[0-9].*[0-9].*[0-9])/))
              {
                F=(F+7)
              }
              /*if(C.match(/.[!,@,#,$,%,^,&,*,?,_,~]/))
              {
                F=(F+5)
              }
              if(C.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/))
              {
                F=(F+7)
              }*/
              if(C.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
              {
                F=(F+6)
              }
              if(C.match(/([a-zA-Z])/)&&C.match(/([0-9])/))
              {
                F=(F+3)
              }
              /*if(C.match(/([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])/))
              {
                F=(F+3)
              }*/

              for(var D=0;D<B.common.length;D++)
              {
                if(C.toLowerCase()==B.common[D])
                {
                  F=-200;
                }
              }
              return F
            }
          })
    })(jQuery)