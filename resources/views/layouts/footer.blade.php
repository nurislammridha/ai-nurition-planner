<div class="mt-10">
    <div class="bg-primary-800 text-white text-center p-4 text-sm md:text-base">
        <p>&copy; 2025 Nutrition Planner. All Rights Reserved.</p>
        <p>
            <a class="underline" href="#">Privacy Policy</a> |
            <a class="underline" href="#">Terms &
                Conditions</a>
        </p>
    </div>
</div>

<script>
    function filterTable() {
        let searchInput = document.getElementById("searchInput").value.toLowerCase();
        let planFilter = document.getElementById("planFilter").value.toLowerCase();
        let rows = document.querySelectorAll("#tableBody tr");

        rows.forEach(row => {
            let name = row.cells[0].textContent.toLowerCase();
            let plan = row.cells[3].textContent.toLowerCase();

            if (name.includes(searchInput) && (planFilter === "" || plan.includes(planFilter))) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
//I worked on romygo project, in app calling feature=>it's running, ai nutrition planner
    function sortTable(colIndex) {
        let table = document.querySelector("table");
        let rows = Array.from(table.rows).slice(1);
        let isAscending = table.dataset.order === "asc";

        rows.sort((a, b) => {
            let valA = parseInt(a.cells[colIndex].textContent.trim()) || 0;
            let valB = parseInt(b.cells[colIndex].textContent.trim()) || 0;
            return isAscending ? valA - valB : valB - valA;
        });

        rows.forEach(row => table.appendChild(row));
        table.dataset.order = isAscending ? "desc" : "asc";
    }

    function confirmDelete(button) {
        if (confirm("Are you sure you want to delete this entry?")) {
            button.closest("tr").remove();
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        new Choices('#healthConditions', { removeItemButton: true });
        new Choices('#allergies', { removeItemButton: true });
    </script>
</body>

</html>