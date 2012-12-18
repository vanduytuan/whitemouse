/*
 * wovodat.js
 * wovodat javascript library
 * author: Van Duy Tuan
 * Created date: 11/6/2012
*/

/*
 * Wovodat javascript object
*/

function Wovodat(){
    
}

/*
 * Create the trim function for the every string object.
 */
Wovodat.trim = function(text){
    return text.replace(/^\s+|\s+$/g,"");
};

/*
 * Get the domain name. Return format is: http://<domain name>
 * No directory name will be appended.
 */
Wovodat.getDomain = function (){
    var location = window.location.toString();
    var domain;
    var protocol = "http://";
    var i,j;
    location = Wovodat.trim(location);
    if(location.length <= protocol.length){
        throw "Invalid domain";
    }
    i = location.indexOf(protocol);
    if(i != 0)
        throw "This is not a Domain";
    i = i + protocol.length;
    j = location.indexOf("/",i);
    if(j == -1) {
        domain = location;
    }else{
        domain = location.substring(0,j);
    }
    return domain;
};
/*
 * This function is to make sure that wovodat javascript file is properly
 * included
 */
Wovodat.displayLibraryMessage = function (){
    return "The wovodat javascript library is properly included";
};
/*
 * Redirect to link location
 */
Wovodat.redirectPage = function(link){
    window.location = link;
};

/*
 * Get the list of all available volcanoes using ajax
 */
Wovodat.getVolcanoList = function(func){
    $.ajax({
        method: "get", 
        url: "/php/switch.php",
        data: "get=VolcanoList",
        success: function(html){
            func({
                list:html
            });
        }
    });
};
/*
 * Get the eruption list of the current volcanos
 */
Wovodat.getEruptionList = function(args){
    var volcano = args.volcano;
    var handler = args.handler;
    volcano = volcano.split("&");
    var cavw = volcano[1];
    $.ajax({
        method: "get", 
        url: "/php/switch.php",
        data: "get=EruptionList&cavw="+cavw,
        success: function(html){
            handler({
                list:html
            });
        }
    });
};

/*
 * Get latitude and longitude of a volcano
 */
Wovodat.getLatLon = function(args){
    var cavw = args.cavw;
    var handler = args.handler;
    $.ajax({
        method: "get", 
        url: "/php/switch.php",
        data: "get=LatLon&cavw="+cavw,
        success: function(html){
            html = html.split(";");
            handler({
                lat:html[0],
                lon:html[1],
                elev:html[2]
            });
        }
    });
};

/*
 * Get available stations for a specific volcano
 * 
 */
Wovodat.getAvailableStations = function (args){
    var cavw = args.cavw;
    var handler = args.handler;
    $.ajax({
        method: "get", 
        url: "/php/switch.php",
        data: "get=AvailableStations&cavw="+cavw,
        success: function(html){
            html = html.split(";");
            if(html[html.length - 1] == "")
                html.length--;
            handler({
                list:html
            });
        }
    });
};

/*
 * Get available stations for a specific type of a specific volcano 
 */

Wovodat.getStations = function(args){
    var cavw = args.cavw;
    var handler = args.handler;
    var type=args.type;
    $.ajax({
        method: "get", 
        url: "/php/switch.php",
        data: "get=Stations&cavw="+cavw + "&type=" + type,
        success: function(html){
            var args = {
                data: html,
                type: type,
                action: 'updateNewData'
            };
            handler(args);
        }
    });
};

/*
 * Get data for a specific station
 */

Wovodat.getStationData = function(args){
    var type=args.type;
    var table = args.table;
    var code = args.code;
    var component = args.component;
    var handler = args.handler;
    $.ajax({
        method:"get",
        url: "/php/switch.php",
        data: "get=StationData&type=" + type.toLowerCase() + "&table=" + table 
        +"&code="+code + "&component=" + component,
        dataType: "json",
        success: function(html){
            handler({
                data: html,
                id: type + "&" + table + "&" + code + "&" + component
            });
        }
    });
};

/*
 * get the javascript date object when user pass in a string of type
 * YYYY-MM-DD HH:II:SS
 */
Wovodat.toDate = function(date){
    //2007-09-12 17:21:01
    var temp = date.split(" ");
    var year = temp[0].split("-");
    var hours = temp[1].split(":");
    return new Date(year[0],year[1],year[2],hours[0],hours[1],hours[2],0);
}