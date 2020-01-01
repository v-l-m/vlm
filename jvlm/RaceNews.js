class RaceNewsHandler
{
  constructor(Title, Message)
  {

    this.Title = Title;
    this.Message = Message;

    this.Show = function()
    {
      $("#RaceNewsBox_Title").text(Title);
      $("#NewsBody1").html(Message);

      $("#RaceNewsBox").modal(
      {
        backdrop: 'static',
        keyboard: false
      });
    };

  }
}