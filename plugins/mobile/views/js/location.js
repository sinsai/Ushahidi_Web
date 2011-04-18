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
    if($.cookie('lat') && $.cookie('lng')) {
      SinsaiLoc.show_addr();
    }
    $("#get_location").click(function(){
      navigator.geolocation.getCurrentPosition(function(result){
        $.cookie('lat',result.coords.latitude);
        $.cookie('lng',result.coords.longitude);
        SinsaiLoc.show_addr();
      });
    });
    $("#del_location").click(function(){
      $("#get_location").text("Detect Location");
      $("#del_location").hide();
      $("#loc_address").text("");
      $.cookie('lat',null);
      $.cookie('lng',null);
    });
  }
});
