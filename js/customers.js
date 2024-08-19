document.addEventListener('DOMContentLoaded', () => {
    loadCustomers();

    document.getElementById('customerForm').removeEventListener('submit', handleCustomerFormSubmit);
    document.getElementById('customerForm').addEventListener('submit', handleCustomerFormSubmit);

    document.getElementById('editCustomerForm').removeEventListener('submit', handleEditFormSubmit);
    document.getElementById('editCustomerForm').addEventListener('submit', handleEditFormSubmit);
});

function showCustomerCreateForm() {
    document.getElementById('customerFormContainer').style.display = 'block';
    document.getElementById('editCustomerFormContainer').style.display = 'none';
}

function handleCustomerFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    const submitButton = event.target.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    fetch('../../api/customers.php', {
        method: 'POST',
        body: JSON.stringify({
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone')
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.message === "Customer created.") {
            loadCustomers();
            document.getElementById('customerForm').reset();
            document.getElementById('customerFormContainer').style.display = 'none';
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        submitButton.disabled = false;
    });
}

function loadCustomers() {
    fetch('../../api/customers.php')
    .then(response => response.json())
    .then(data => {
        const customersTableBody = document.getElementById('customersTableBody');
        customersTableBody.innerHTML = '';

        data.forEach(customer => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${customer.customer_id}</td>
                <td>${customer.name}</td>
                <td>${customer.email}</td>
                <td>${customer.phone}</td>
                <td>
                    <button onclick="showEditCustomerForm(${customer.customer_id})">Edit</button>
                    <button onclick="deleteCustomer(${customer.customer_id})">Delete</button>
                </td>
            `;
            customersTableBody.appendChild(row);
        });
    })
    .catch(error => console.error('Error:', error));
}

function showEditCustomerForm(id) {
    fetch(`../../api/customers.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.customer_id) {
            document.getElementById('edit_customer_id').value = data.customer_id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_phone').value = data.phone;
            document.getElementById('customerFormContainer').style.display = 'none';
            document.getElementById('editCustomerFormContainer').style.display = 'block';
        } else {
            alert('Customer not found.');
        }
    })
    .catch(error => console.error('Error:', error));
}

function handleEditFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    const submitButton = event.target.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    fetch('../../api/customers.php', {
        method: 'PUT',
        body: JSON.stringify({
            customer_id: formData.get('customer_id'),
            name: formData.get('name'),
            email: formData.get('email'),
            phone: formData.get('phone')
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.message === "Customer updated.") {
            loadCustomers();
            document.getElementById('editCustomerForm').reset();
            document.getElementById('editCustomerFormContainer').style.display = 'none';
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        submitButton.disabled = false;
    });
}

function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        fetch(`../../api/customers.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.message === "Customer deleted.") {
                loadCustomers();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
