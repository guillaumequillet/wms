// incomingForm
let rowNumber = 0;
let orderRow = document.querySelector('#incomingForm div.orderRow');
let orderRowsParent = orderRow.parentNode;
orderRowsParent.removeChild(orderRow);
let addButton = document.querySelector("#addRowButton");

function addRow() {
    let newChild = orderRow.cloneNode(true);
    orderRowsParent.appendChild(newChild);
    let totalRows = orderRowsParent.querySelectorAll("div.orderRow").length;

    Array.from(newChild.querySelectorAll("div.orderRow input")).forEach(function(element, i) {
        element.name = element.name + rowNumber;
    });

    rowNumber++;

    let button = newChild.querySelector(".button");

    button.addEventListener("click", e => {
        e.preventDefault();
        orderRowsParent.removeChild(newChild);
    })    
}

// adding at least one line
// addRow(); 

addButton.addEventListener("click", e => {
    e.preventDefault();
    addRow();
});