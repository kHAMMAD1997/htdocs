let defaultRowCount = 3; // No of rows
let defaultColCount = 5; // No of cols
const SPREADSHEET_DB = "table-1";

initializeData = () => {
  const data = [];
  for (let i = 0; i <= defaultRowCount; i++) {
    const child = [];
    for (let j = 0; j <= defaultColCount; j++) {
      child.push("");
    }
    data.push(child);
  }
  return data;
};

getData = () => {
  let data = localStorage.getItem(SPREADSHEET_DB);
  if (data === undefined || data === null) {
    return initializeData();
  }
  return JSON.parse(data);
};

saveData = data => {
  localStorage.setItem(SPREADSHEET_DB, JSON.stringify(data));
};

resetData = data => {
  localStorage.removeItem(SPREADSHEET_DB);
  this.createSpreadsheet();
};

createHeaderRow = () => {
  const columnNames = [
    "Budget Line Description",
    "Quantity",
    "Unit Cost (USD)",
    "Duration (Monthly)",
    "Total Cost (USD)"
  ];

  const tr = document.createElement("tr");
  tr.setAttribute("id", "h-0");
  for (let i = 0; i <= defaultColCount; i++) {
    const th = document.createElement("th");
    th.setAttribute("id", `h-0-${i}`);
    th.setAttribute("class", `${i === 0 ? "" : "column-header"}`);
    
    if (i !== 0) {
      const span = document.createElement("span");
      span.innerHTML = columnNames[i - 1] || `Col ${i}`;
      span.setAttribute("class", "column-header-span");

      const dropDownDiv = document.createElement("div");
      dropDownDiv.setAttribute("class", "dropdown");
      dropDownDiv.innerHTML = `<button class="dropbtn" id="col-dropbtn-${i}"></button>
        <div id="col-dropdown-${i}" class="dropdown-content">
          <p class="col-insert-left">Insert 1 column left</p>
          <p class="col-insert-right">Insert 1 column right</p>
          <p class="col-delete">Delete column</p>
        </div>`;

      th.appendChild(span);
      th.appendChild(dropDownDiv);
    }
    tr.appendChild(th);
  }
  return tr;
};

createTableBodyRow = rowNum => {
  const tr = document.createElement("tr");
  tr.setAttribute("id", `r-${rowNum}`);
  for (let i = 0; i <= defaultColCount; i++) {
    const cell = document.createElement(`${i === 0 ? "th" : "td"}`);
    cell.contentEditable = false; // Disable content editing for all cells

    if (i === 0) {
      const span = document.createElement("span");
      const dropDownDiv = document.createElement("div");
      span.innerHTML = rowNum;
      dropDownDiv.setAttribute("class", "dropdown");
      dropDownDiv.innerHTML = `<button class="dropbtn" id="row-dropbtn-${rowNum}"></button>
        <div id="row-dropdown-${rowNum}" class="dropdown-content">
          <p class="row-insert-top">Insert 1 row above</p>
          <p class="row-insert-bottom">Insert 1 row below</p>
          <p class="row-delete">Delete row</p>
        </div>`;
      cell.appendChild(span);
      cell.appendChild(dropDownDiv);
      cell.setAttribute("class", "row-header");
    }
    cell.setAttribute("id", `r-${rowNum}-${i}`);
    tr.appendChild(cell);
  }
  return tr;
};

createTableBody = tableBody => {
  for (let rowNum = 1; rowNum <= defaultRowCount; rowNum++) {
    tableBody.appendChild(this.createTableBodyRow(rowNum));
  }
};

populateTable = () => {
  const data = this.getData();
  if (data === undefined || data === null) return;

  for (let i = 1; i < data.length; i++) {
    for (let j = 1; j < data[i].length; j++) {
      const cell = document.getElementById(`r-${i}-${j}`);
      cell.innerHTML = data[i][j];
    }
  }
};

createSpreadsheet = () => {
  const spreadsheetData = this.getData();
  defaultRowCount = spreadsheetData.length - 1 || defaultRowCount;
  defaultColCount = spreadsheetData[0].length - 1 || defaultColCount;

  const tableHeaderElement = document.getElementById("table-headers");
  const tableBodyElement = document.getElementById("table-body");

  const tableBody = tableBodyElement.cloneNode(true);
  tableBodyElement.parentNode.replaceChild(tableBody, tableBodyElement);
  const tableHeaders = tableHeaderElement.cloneNode(true);
  tableHeaderElement.parentNode.replaceChild(tableHeaders, tableHeaderElement);

  tableHeaders.innerHTML = "";
  tableBody.innerHTML = "";

  tableHeaders.appendChild(createHeaderRow(defaultColCount));
  createTableBody(tableBody, defaultRowCount, defaultColCount);

  populateTable();
};

// Initialize the spreadsheet
createSpreadsheet();
