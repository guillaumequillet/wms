// incomingForm
let orderRow = document.querySelector('#incomingForm div.orderRow');
let orderRowsParent = orderRow.parentNode;
let addButton = document.querySelector("#addRowButton");

addButton.addEventListener("click", e=> {
    e.preventDefault();
    orderRowsParent.appendChild(orderRow.cloneNode(true));
});