$(document).ready(function() {
    // incomingForm
    let feedbackElmt = document.querySelector("#feedbackIncomingForm");
    $((feedbackElmt)).hide();

    let formElmt = document.querySelector("#incomingForm");
    let rowNumber = 0;
    let orderRow = document.querySelector('#incomingForm div.orderRow');
    let orderRowsParent = orderRow.parentNode;
    orderRowsParent.removeChild(orderRow);
    let addButton = document.querySelector("#addRowButton");
    let currentRow = null;

    formElmt.addEventListener('submit', e => {
        feedbackElmt.innerText = '';
        $((feedbackElmt)).hide();

        Array.from(document.querySelectorAll('input')).forEach(i => {
            if (i.type !== 'submit' && i.value === '') {
                feedbackElmt.innerText = 'Tous les champs doivent être remplis.'
                $((feedbackElmt)).show();
                e.preventDefault();
            }
        });
    });

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

        // to remove the line with the delete button
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

        // location field
        let locationField = Array.from(newChild.querySelectorAll("div.orderRow input"))[2];
        let locationSuggestions = Array.from(newChild.querySelectorAll("ul"))[1];
        
        locationField.addEventListener('input', function() {
            if (locationField.value !== '') {
                $.post('/location/suggestions', {concatenate:locationField.value}, function(data) {
                    if (data !== 'no-result') {
                        deleteListItems(locationSuggestions);

                        // creating the list using results from AJAX
                        data.split(';').forEach(function(element) {
                            let resultElmt = document.createElement("li");
                            resultElmt.classList.add("dynamicListItem");
                            resultElmt.innerText = element;
                            locationSuggestions.appendChild(resultElmt);
                            
                            // we set value to clicked choice
                            resultElmt.addEventListener("click", e => {
                                locationField.value = resultElmt.innerText;
                                deleteListItems(locationSuggestions);                              
                            });
                        });
                        
                    } else {
                        // if no result from query string
                        deleteListItems(locationSuggestions);
                        let noResultElmt = document.createElement("li");
                        noResultElmt.innerText = "Pas de résultat.";
                        locationSuggestions.appendChild(noResultElmt);
                    }
                });
            } else {
                // if the query string is empty
                deleteListItems(locationSuggestions);
            }
        });
    }

    // to add some line
    addButton.addEventListener("click", e => {
        e.preventDefault();
        addRow();
    });
});
