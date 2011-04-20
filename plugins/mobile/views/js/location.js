SinsaiLoc = {
  show_addr: function(){
    $("#get_location").text("Update Location");
    $("#del_location").show();
    $("#loc_address").text($.cookie('lat')+", "+$.cookie('lng'));
  }
}

$(document).ready(function(){
  if(!navigator.geolocation){
    $("#location_bar").hide();
    $("#navigation").css("margin-bottom","5px");
  }else{
    $("#del_location").hide();
    if($.cookie('lat') && $.cookie('lng') && $.cookie('lat') != "na" && $.cookie('lng') != "na") {
      SinsaiLoc.show_addr();
    }else{
      $.cookie('lat',"na", {path: "/"});
      $.cookie('lng',"na", {path: "/"});
    }
    $("#get_location").click(function(){
      navigator.geolocation.getCurrentPosition(function(result){
        $.cookie('lat',result.coords.latitude, {path: "/"});
        $.cookie('lng',result.coords.longitude, {path: "/"});
        SinsaiLoc.show_addr();
        location.reload();
      });
    });
    $("#del_location").click(function(){
      $("#get_location").text("Detect Location");
      $("#del_location").hide();
      $("#loc_address").text("");
      $.cookie('lat',"na", {path: "/"});
      $.cookie('lng',"na", {path: "/"});
      location.reload();
    });
  }
});
