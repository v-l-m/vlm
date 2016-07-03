//
// Module for handling boat polars
//

function PolarManagerClass()
{
    this.Polars =[];

    this.Init = function()
        {
            this.Polars=new Array();
            // Bg load the list of boats with a polar in VLM
            $.get("/ws/polarlist.php",
                    function (Data)
                    {
                        //Parse WS data, build polarlist and URL
                        // Build list of boat for lazy loading
                        for (index in Data.list)
                        {
                            this.Polars[Data.list[index]]=null;
                        }
                    }
                )
        }

    this.GetPolarAtSpeed= function(Boat,WindSpeed)
        {
            throw "Not ready yet";
        }

}