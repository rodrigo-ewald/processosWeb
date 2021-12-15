const searchInput = document.getElementById("search");
const rows = document.querySelectorAll("tbody tr");
console.log(rows);
searchInput.addEventListener("keyup", function(event) {
  const q = event.target.value.toLowerCase();
  rows.forEach((row) => {
    row.querySelector("a").textContent.toLowerCase().startsWith(q) ?
      (row.style.display = "table-row") :
      (row.style.display = "none");
  });
});
