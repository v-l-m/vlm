class MsgBox
{
  static get MSGBOX_OKONLY()
  {
    return 0;
  }

  static get MSGBOX_YESNO()
  {
    return 1;
  }

  constructor()
  {

    this.OkYesCallBack = null;
    this.NoCancelCallBack = null;

    this.Btn1Function = function()
    {
      $("#MsgBoxDialog").modal("hide");
      if (this.OkYesCallBack)
      {
        setTimeout(this.OkYesCallBack, 100);
      }
    };

    this.Btn2Function = function()
    {
      $("#MsgBoxDialog").modal("hide");
      if (this.NoCancelCallBack)
      {
        setTimeout(this.NoCancelCallBack, 100);
      }
    };

    this.SetupButtons = function(Type)
    {
      let BtnVisiblity = [true, false];
      let Btns = ["#MsgBoxButton1", "#MsgBoxButton2"];
      let BtnsMsg = ["OK", ""];

      switch (Type)
      {

        case MsgBox.MSGBOX_YESNO:
          BtnsMsg = [GetLocalizedString("yes"), GetLocalizedString("no")];
          BtnVisiblity = [true, true];
          break;

        default:

      }

      for (let index in Btns)
      {
        $(Btns[index]).text(BtnsMsg[index]).addClass("hidden");
        if (BtnVisiblity[index])
        {
          $(Btns[index]).removeClass("hidden");
        }
      }

    };

    this.Show = function(Type, Title, Msg, OkYesCallBack, NoCancelCallBack)
    {

      this.SetupButtons(Type);

      this.OkYesCallBack = OkYesCallBack;
      this.NoCancelCallBack = NoCancelCallBack;

      $("#MsgBoxTitle").text(Title);
      $("#MsgBoxText").html(Msg);
      $("#MsgBoxButton1").off('click');
      $("#MsgBoxButton2").off('click');
      $("#MsgBoxButton1").on('click', this.Btn1Function.bind(this));
      $("#MsgBoxButton2").on('click', this.Btn2Function.bind(this));
      let forms = $(".modal .in").modal("hide");

      $("#MsgBoxDialog").modal("show");
    };
  }
}