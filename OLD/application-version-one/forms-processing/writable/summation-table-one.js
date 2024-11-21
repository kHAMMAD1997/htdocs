document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("table-body");
    const subtotalElement = document.getElementById("Sub-total-table-0");

    // Function to calculate and update the total cost for a specific row
    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector(`#${row.id}-2`).innerText) || 0;
        const unitCost = parseFloat(row.querySelector(`#${row.id}-3`).innerText) || 0;
        const duration = parseFloat(row.querySelector(`#${row.id}-4`).innerText) || 0;
        const totalCost = quantity * unitCost * duration;

        const totalCostCell = row.querySelector(`#${row.id}-5`);
        totalCostCell.innerText = totalCost.toFixed(2); // Display with two decimal places
        return totalCost;
    }

    // Function to calculate the subtotal of all rows
    function updateSubtotal() {
        let subtotal = 0;
        const rows = tableBody.querySelectorAll("tr");
        rows.forEach(row => {
            subtotal += calculateRowTotal(row);
        });
        subtotalElement.innerText = `${subtotal.toFixed(2)} USD`;
    }

    // Function to update event listeners on each cell for recalculation
    function updateEventListeners() {
        tableBody.querySelectorAll("td").forEach(cell => {
            cell.addEventListener("input", handleCellInput);
        });
    }

    // Event handler for cell input to trigger recalculation
    function handleCellInput(event) {
        const cell = event.target;
        const rowId = cell.id.split("-").slice(0, 2).join("-"); // Extract row id from cell id
        const row = document.getElementById(rowId);
        calculateRowTotal(row);
        updateSubtotal();
    }

    // Modify the addRow function to call updateEventListeners after adding a new row
    window.addRow = (currentRow, direction) => {
        let data = getData();
        const colCount = data[0].length;
        const newRow = new Array(colCount).fill("");
        if (direction== "top") {
            data.splice(currentRow, 0, newRow);
        } else if (direction== "bottom") {
            data.splice(currentRow + 1, 0, newRow);
        }
        defaultRowCount++;
        saveData(data);
        createSpreadsheet(); // Recreate the table to include new rows
        updateEventListeners(); // Bind event listeners to new cells
    };

    // Initial setup
    updateSubtotal();
    updateEventListeners();
});
