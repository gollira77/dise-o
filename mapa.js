function iniciarMap(){
    var coord = {lat:-26.1452581 ,lng: -58.1592725};
    var map = new google.maps.Map(document.getElementById('map'),{
      zoom: 16,
      center: coord
    });
    var marker = new google.maps.Marker({
      position: coord,
      map: map
    });
}