SinsaiLoc = {
  show_addr: function(){
    $("#get_location").text("Update Location");
    $("#del_location").show(window.localStorage.sinsailat+", "+window.localStorage.sinsailng);
    $("#loc_address").text(result);
  }
}

$(document).ready(function(){
  if(!navigator.geolocation){
    $("#location_bar").hide();
    $("#navigation").css("margin-bottom","5px");
  }else{
    $("#del_location").hide();
    if(window.localStorage.sinsailat && window.localStorage.sinsailng) {
      SinsaiLoc.show_addr();
    }
    $("#get_location").click(function(){
      navigator.geolocation.getCurrentPosition(function(result){
        window.localStorage.sinsailat = result.coords.latitude;
        window.localStorage.sinsailng = result.coords.longitude;
        SinsaiLoc.show_addr();
      });
    });
    $("#del_location").click(function(){
      $("#get_location").text("Detect Location");
      $("#del_location").hide();
      $("#loc_address").text("");
      window.localStorage.sinsailat = undefined;
      window.localStorage.sinsailng = undefined;
    });
  }
});
