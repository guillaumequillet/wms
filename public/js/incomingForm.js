$(document).ready(function() {
    class IncomingForm
    {
        constructor() 
        {
            this.feedbackElmt = document.querySelector("#feedbackIncomingForm");
            $((this.feedbackElmt)).hide();
        
            this.rowNumber = 0;
        
            // we take orderRow original element to clone it and remove it
            this.orderRow = document.querySelector('#incomingForm div.orderRow');
            this.orderRowsParent = this.orderRow.parentNode;
            this.orderRowsParent.removeChild(this.orderRow);
        
            // to add some line
            this.addButton = document.querySelector("#addRowButton");
            this.addButton.addEventListener("click", e => {
                e.preventDefault();
                this.addRow();
            });
        
            // set validation
            this.formElmt = document.querySelector("#incomingForm");

            this.formElmt.addEventListener('submit', e => {
                // let result = !this.checkRows(); 

                // if (!result) {
                //     e.preventDefault();
                //     $((this.feedbackElmt)).show();
                // } else {
                //     $((this.feedbackElmt)).hide();
                // }
            });
        }

        checkRows() {
            let inputs = Array.from(document.querySelectorAll('input'));
        
            // if no article was set
            if (inputs.length === 4) {
                this.feedbackElmt.innerText = "Vous devez ajouter au moins une ligne.";
                return false;
            }

            // the form can only be validated if all filled and all the articles/locations exist
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].type !== 'submit' && inputs[i].value === '') {
                    this.feedbackElmt.innerText = 'Tous les champs doivent être remplis.';
                    return false;
                } else if (inputs[i].type !== 'submit' && inputs[i].value !== '') {
                    let action = '';
                    if (inputs[i].name.includes('article')) {
                        action = 'article';
                    } else if (inputs[i].name.includes('location')) {
                        action = 'location';
                    }

                    if (action !== '') {
                        let url = `/${action}/exists`;
                        let postObject = {};
                        
                        if (action === 'article') {
                            postObject = {code:inputs[i].value};
                        } else if (action === 'location') {
                            postObject = {location:inputs[i].value};
                        }

                        if (action === 'article' || action === 'location')
                        {
                            $.post(url, postObject, (data) => {
                                if (data === 'false') {
                                    $('#feedbackElmt').text(`L'élément "${action}" ${inputs[i].value} n'existe pas.`);
                                }  
                            });  
                        }
                    }
                }
            };

            // no error occured
            this.feedbackElmt.innerText = '';
            $((this.feedbackElmt)).hide();
            return true;
        }
            
        addRow() {
            let newChild = this.orderRow.cloneNode(true);
            this.orderRowsParent.appendChild(newChild);
        
            Array.from(newChild.querySelectorAll("div.orderRow input")).forEach( element => {
                element.name = element.name + this.rowNumber;
            });
        
            this.rowNumber++;
        
            // to remove the line with the delete button
            let button = newChild.querySelector(".button");
            
            button.addEventListener("click", e => {
                e.preventDefault();
                this.orderRowsParent.removeChild(newChild);
            })    
        
            // article field
            let articleField = Array.from(newChild.querySelectorAll("div.orderRow input"))[0];
            let articleSuggestions = Array.from(newChild.querySelectorAll("ul"))[0];
            
            articleField.addEventListener('input', () => {
                if (articleField.value !== '') {
                    $.post('/article/suggestions', {code:articleField.value}, (data) => {
                        if (data !== 'no-result') {
                            this.deleteListItems(articleSuggestions);
        
                            // creating the list using results from AJAX
                            data.split(';').forEach((element) => {
                                let resultElmt = document.createElement("li");
                                resultElmt.classList.add("dynamicListItem");
                                resultElmt.innerText = element;
                                articleSuggestions.appendChild(resultElmt);
                                
                                // we set value to clicked choice
                                resultElmt.addEventListener("click", e => {
                                    articleField.value = resultElmt.innerText;
                                    this.deleteListItems(articleSuggestions);                              
                                });
                            });
                            
                        } else {
                            // if no result from query string
                            this.deleteListItems(articleSuggestions);
                            let noResultElmt = document.createElement("li");
                            noResultElmt.innerText = "Pas de résultat.";
                            articleSuggestions.appendChild(noResultElmt);
                        }
                    });
                } else {
                    // if the query string is empty
                    this.deleteListItems(articleSuggestions);
                }
            });
        
            // location field
            let locationField = Array.from(newChild.querySelectorAll("div.orderRow input"))[2];
            let locationSuggestions = Array.from(newChild.querySelectorAll("ul"))[1];
            
            locationField.addEventListener('input', () => {
                if (locationField.value !== '') {
                    $.post('/location/suggestions', {concatenate:locationField.value}, (data) => {
                        if (data !== 'no-result') {
                            this.deleteListItems(locationSuggestions);
        
                            // creating the list using results from AJAX
                            data.split(';').forEach((element) => {
                                let resultElmt = document.createElement("li");
                                resultElmt.classList.add("dynamicListItem");
                                resultElmt.innerText = element;
                                locationSuggestions.appendChild(resultElmt);
                                
                                // we set value to clicked choice
                                resultElmt.addEventListener("click", e => {
                                    locationField.value = resultElmt.innerText;
                                    this.deleteListItems(locationSuggestions);                              
                                });
                            });
                            
                        } else {
                            // if no result from query string
                            this.deleteListItems(locationSuggestions);
                            let noResultElmt = document.createElement("li");
                            noResultElmt.innerText = "Pas de résultat.";
                            locationSuggestions.appendChild(noResultElmt);
                        }
                    });
                } else {
                    // if the query string is empty
                    this.deleteListItems(locationSuggestions);
                }
            });
        }

        deleteListItems(listElmt) {
            Array.from(listElmt.querySelectorAll("li")).forEach((element) => {
                listElmt.removeChild(element);
            });            
        }
    }

    new IncomingForm();
});
