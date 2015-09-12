var claps = {};

claps.toggle = function(id) {
  id = id.replace("#", "");
  if (document.getElementById(id).style.display == "inline-block" || document.getElementById(id).style.display == "block") {
    document.getElementById(id).style.display = "none";
  }
  else {
    document.getElementById(id).style.display = "inline-block";
  }
}

claps.hide = function(id) {
  id = id.replace("#", "");
  document.getElementById(id).style.display = "none";
}

claps.show = function(id) {
  id = id.replace("#", "");
  document.getElementById(id).style.display = "inline-block";
}