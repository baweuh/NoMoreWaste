let currentLanguage = 'fr'; // Langue par défaut
let translations = {}; // Conteneur pour les traductions

// Fonction pour charger les traductions d'une langue donnée
function loadLanguage(lang) {
    fetch(`../lang/${lang}.json`)
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
    const pageTitleElement = document.getElementById("pageTitle");
    const createButtonElement = document.getElementById("createButton");
    const languageCodeHeader = document.getElementById("languageCodeHeader");
    const languageTitleHeader = document.getElementById("languageTitleHeader");
    const actionsHeader = document.getElementById("actionsHeader");
    const formTitleElement = document.getElementById("formTitle");
    const languageNameLabel = document.getElementById("languageNameLabel");
    const languageContentLabel = document.getElementById("languageContentLabel");
    const formSubmitButton = document.getElementById("formSubmitButton");
    const Edit = document.getElementById("Edit");
    const Delete = document.getElementById("Delete");

    if (pageTitleElement) pageTitleElement.innerText = translations.title || 'Manage Languages';
    if (createButtonElement) createButtonElement.innerText = translations.create_language || 'Create New Language';

    if (languageCodeHeader) languageCodeHeader.innerText = translations.language_code || 'Language Code';
    if (languageTitleHeader) languageTitleHeader.innerText = translations.language_title || 'Language Title';
    if (actionsHeader) actionsHeader.innerText = translations.actions || 'Actions';

    if (formTitleElement) formTitleElement.innerText = translations.form_title_create || 'Create Language';
    if (languageNameLabel) languageNameLabel.innerText = translations.language_name || 'Language Name';
    if (languageContentLabel) languageContentLabel.innerText = translations.language_content || 'Content (JSON):';
    if (formSubmitButton) formSubmitButton.innerText = translations.submit_create || 'Create';
    
    if (Edit) Edit.innerText = translations.edit || 'Edit';
    if (Delete) Delete.innerText = translations.delete || 'Delete';
}

// Fonction pour charger les langues depuis le serveur
function loadLanguages() {
    fetch("../api/languages.php")
        .then(response => response.json())
        .then(data => {
            console.log("Data from API:", data); // Ajoutez ceci pour déboguer
            const tableBody = document.getElementById("languagesTableBody");
            tableBody.innerHTML = "";

            // Assurez-vous que la structure des données est correcte
            if (Array.isArray(data)) {
                data.forEach(language => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${language.code}</td>
                        <td>${language.name}</td>
                        <td>
                            <button id="Edit" onclick="editLanguage('${language.code}')">${translations.edit}</button>
                            <button id="Delete" onclick="deleteLanguage('${language.code}')">${translations.delete}</button>
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
    const formElement = document.getElementById("languageForm");
    const languageFormContainer = document.getElementById("languageFormContainer");

    if (formTitleElement) {
        formTitleElement.innerText = translations.form_title_create || 'Create Language';
    }
    if (formElement) {
        formElement.reset();
        document.getElementById("languageCode").value = "";
        document.getElementById("languageName").value = "";
        document.getElementById("languageContent").value = "{}";
        if (document.getElementById("formSubmitButton")) {
            document.getElementById("formSubmitButton").innerText = translations.submit_create || 'Create';
        }
    }
    if (languageFormContainer) {
        languageFormContainer.style.display = "block";
    }
}

// Fonction pour gérer la soumission du formulaire de langue
function handleLanguageFormSubmit(event) {
    event.preventDefault();

    const formData = {
        code: document.getElementById("languageCode").value,
        name: document.getElementById("languageName").value,
        content: JSON.parse(document.getElementById("languageContent").value)
    };

    const method = formData.code ? "PUT" : "POST";
    const url = "../api/languages.php" + (formData.code ? `?code=${formData.code}` : "");

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
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
    fetch(`../api/languages.php?code=${code}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("formTitle").innerText = translations.form_title_edit || 'Edit Language';
            document.getElementById("languageCode").value = code;
            document.getElementById("languageName").value = data.language_name || '';
            document.getElementById("languageContent").value = JSON.stringify(data.content || {}, null, 2);
            document.getElementById("formSubmitButton").innerText = translations.submit_update || 'Update';
            document.getElementById("languageFormContainer").style.display = "block";
        })
        .catch(error => console.error("Error:", error));
}

// Fonction pour supprimer une langue
function deleteLanguage(code) {
    if (confirm(translations.confirm_delete || "Are you sure you want to delete this language?")) {
        fetch(`../api/languages.php?code=${code}`, {
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
