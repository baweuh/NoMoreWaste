let currentLanguage = 'fr'; // Langue par défaut
let translations = {}; // Conteneur pour les traductions

// Fonction pour charger les traductions d'une langue donnée
function loadLanguage(lang) {
    fetch(`../../../../lang/${lang}.json`)
        .then(response => response.json())
        .then(data => {
            translations = data;
            updatePageTexts();
            currentLanguage = lang;
        })
        .catch(error => console.error('Error loading language:', error));
}

// Fonction pour mettre à jour les textes de la page en fonction des traductions chargées
function updatePageTexts() {
    const ManageLanguagesBackoffice = document.getElementById("ManageLanguagesBackoffice");
    const createButtonElement = document.getElementById("createButton");
    const languageCodeHeader = document.getElementById("languageCodeHeader");
    const languageTitleHeader = document.getElementById("languageTitleHeader");
    const actionsHeader = document.getElementById("actionsHeader");
    const formTitleElement = document.getElementById("formTitle");
    const languageNameLabel = document.getElementById("languageNameLabel");
    const languageContentLabel = document.getElementById("languageContentLabel");
    const formSubmitButton = document.getElementById("formSubmitButton");

    if (ManageLanguagesBackoffice) ManageLanguagesBackoffice.innerText = translations.title || 'Manage Languages';
    if (createButtonElement) createButtonElement.innerText = translations.create_language || 'Create New Language';

    if (languageCodeHeader) languageCodeHeader.innerText = translations.language_code || 'Language Code';
    if (languageTitleHeader) languageTitleHeader.innerText = translations.language_title || 'Language Title';
    if (actionsHeader) actionsHeader.innerText = translations.actions || 'Actions';

    if (formTitleElement) formTitleElement.innerText = translations.form_title_create || 'Create Language';
    if (languageNameLabel) languageNameLabel.innerText = translations.language_name || 'Language Name';
    if (languageContentLabel) languageContentLabel.innerText = translations.language_content || 'Content';
    if (formSubmitButton) formSubmitButton.innerText = translations.submit_create || 'Create';
}

// Fonction pour charger les langues depuis le serveur
function loadLanguages() {
    fetch("../../../../api/languages.php")
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("languagesTableBody");
            tableBody.innerHTML = "";

            if (Array.isArray(data)) {
                data.forEach(language => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${language.code}</td>
                        <td>${language.name}</td>
                        <td>
                            <button onclick="editLanguage('${language.code}')">${translations.edit || 'Edit'}</button>
                            <button onclick="deleteLanguage('${language.code}')">${translations.delete || 'Delete'}</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error("Invalid data format:", data);
            }
        })
        .catch(error => console.error("Error loading languages:", error));
}

// Fonction pour afficher le formulaire de création de langue
function showLanguageCreateForm() {
    const formTitleElement = document.getElementById("formTitle");
    const languageFormContainer = document.getElementById("languageFormContainer");
    const formElement = document.getElementById("languageForm");

    if (formTitleElement) {
        formTitleElement.innerText = translations.form_title_create || 'Create Language';
    }

    formElement.innerHTML = '<input type="hidden" id="languageCode" name="languageCode" />'; // Reset form

    // Ajout des éléments du formulaire dynamiquement
    addFormElements({}, true);
    document.getElementById("formSubmitButton").innerText = translations.submit_create || 'Create';
    
    if (languageFormContainer) {
        languageFormContainer.style.display = "block";
    }
}

// Fonction pour ajouter dynamiquement des éléments du formulaire
function addFormElements(content, isNew = false) {
    const formElement = document.getElementById("languageForm");
    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.id = "formSubmitButton";
    submitButton.innerText = isNew ? (translations.submit_create || 'Create') : (translations.submit_update || 'Update');

    // Vérifier que content est un objet
    if (typeof content === 'object' && content !== null) {
        for (const [key, value] of Object.entries(content)) {
            const label = document.createElement("label");
            label.htmlFor = key;
            label.innerText = key;

            const input = document.createElement("input");
            input.type = "text";
            input.id = key;
            input.name = key;
            input.value = value;

            formElement.appendChild(label);
            formElement.appendChild(input);
        }
    } else {
        console.warn('Content is not a valid object:', content);
    }
    formElement.appendChild(submitButton);
}

// Fonction pour gérer la soumission du formulaire de langue
function handleLanguageFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = {};
    formData.forEach((value, key) => {
        if (key !== "languageCode") {
            data[key] = value;
        }
    });

    const jsonData = {
        code: document.getElementById("languageCode").value,
        content: data
    };

    const method = jsonData.code ? "PUT" : "POST";
    const url = "../../../../api/languages.php" + (jsonData.code ? `?code=${jsonData.code}` : "");

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        loadLanguages();
        document.getElementById("languageFormContainer").style.display = "none";
    })
    .catch(error => console.error("Error:", error));
}

// Fonction pour éditer une langue
function editLanguage(code) {
    fetch(`../../../../api/languages.php?code=${code}`)
        .then(response => response.json())
        .then(data => {
            const formTitleElement = document.getElementById("formTitle");
            formTitleElement.innerText = translations.form_title_edit || 'Edit Language';

            const formElement = document.getElementById("languageForm");
            formElement.innerHTML = '<input type="hidden" id="languageCode" name="languageCode" value="' + code + '" />';

            // Ajout des éléments du formulaire dynamiquement
            addFormElements(data.content || {}, false);
            
            document.getElementById("languageFormContainer").style.display = "block";
        })
        .catch(error => console.error("Error:", error));
}

// Fonction pour supprimer une langue
function deleteLanguage(code) {
    if (confirm(translations.confirm_delete || "Are you sure you want to delete this language?")) {
        fetch(`../../../../api/languages.php?code=${code}`, {
            method: "DELETE"
        })
        .then(response => response.json())
        .then(data => {
            loadLanguages();
        })
        .catch(error => console.error("Error:", error));
    }
}

// Fonction pour changer la langue
function changeLanguage(lang) {
    loadLanguage(lang);
}

// Initialisation lors du chargement de la page
document.addEventListener("DOMContentLoaded", function () {
    const userLang = navigator.language || navigator.userLanguage;
    const lang = ['fr', 'de', 'es', 'it'].includes(userLang.substring(0, 2)) ? userLang.substring(0, 2) : 'en';
    loadLanguage(lang);
    loadLanguages();

    const languageForm = document.getElementById("languageForm");
    if (languageForm) {
        languageForm.addEventListener("submit", handleLanguageFormSubmit);
    } else {
        console.error("Language form not found");
    }

    const languageSelect = document.getElementById("languageSelect");
    if (languageSelect) {
        languageSelect.addEventListener("change", (event) => {
            changeLanguage(event.target.value);
        });
    }
});
