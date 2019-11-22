$(document).ready(function() {
    // incomingForm
    let rowNumber = 0;
    let orderRow = document.querySelector('#incomingForm div.orderRow');
    let orderRowsParent = orderRow.parentNode;
    orderRowsParent.removeChild(orderRow);
    let addButton = document.querySelector("#addRowButton");

    function deleteListItems(listElmt) {
        Array.from(listElmt.querySelectorAll("li")).forEach(function(element) {
            listElmt.removeChild(element);
        });            
    }

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

        // article field
        let articleField = Array.from(newChild.querySelectorAll("div.orderRow input"))[0];
        let articleSuggestions = Array.from(newChild.querySelectorAll("ul"))[0];
        
        articleField.addEventListener('input', function() {
            if (articleField.value !== '') {
                $.post('/article/suggestions', {code:articleField.value}, function(data) {
                    if (data !== 'no-result') {
                        deleteListItems(articleSuggestions);

                        // creating the list using results from AJAX
                        data.split(';').forEach(function(element) {
                            let resultElmt = document.createElement("li");
                            resultElmt.classList.add("dynamicListItem");
                            resultElmt.innerText = element;
                            articleSuggestions.appendChild(resultElmt);
                            
                            // we set value to clicked choice
                            resultElmt.addEventListener("click", e => {
                                articleField.value = resultElmt.innerText;
                                deleteListItems(articleSuggestions);                              
                            });
                        });
                        
                    } else {
                        // if no result from query string
                        deleteListItems(articleSuggestions);
                        let noResultElmt = document.createElement("li");
                        noResultElmt.innerText = "Pas de résultat.";
                        articleSuggestions.appendChild(noResultElmt);
                    }
                });
            } else {
                // if the query string is empty
                deleteListItems(articleSuggestions);
            }
        });
    }

    addButton.addEventListener("click", e => {
        e.preventDefault();
        addRow();
    });
});
