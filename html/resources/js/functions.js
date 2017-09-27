function filterGames() {
  var input = document.getElementById("searchBox").value.toUpperCase();
  var gameTable = document.getElementById("gameTable");
  var columnList = gameTable.getElementsByTagName("tr");

  for (var i = 0; i < columnList.length; i++) {
    var gameColumn = columnList[i].getElementsByTagName("td");
    if (gameColumn[1]) {
      if (gameColumn[1].innerHTML.toUpperCase().indexOf(input) > -1) {
        columnList[i].style.display = "";
      } else {
        columnList[i].style.display = "none";
      }
    }
  }
}

function hideToggle() {
  var toggleElement = document.getElementById("toggle");
  var rightArrow = document.getElementById("rightArrow");
  var downArrow = document.getElementById("downArrow");

  if (toggleElement.style.display === "none") {
    toggleElement.style.display = "";
    rightArrow.style.display = "none";
    downArrow.style.display = "";
  } else {
    toggleElement.style.display = "none";
    rightArrow.style.display = "";
    downArrow.style.display = "none";
  }

}
