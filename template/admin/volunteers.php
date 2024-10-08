<!DOCTYPE html>
<html lang="en" data-default-lang="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Volunteers</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <h1>Manage Volunteers</h1>
    <button onclick="showVolunteerCreateForm()">Create New Volunteer</button>
    <table id="volunteersTable">
        <thead>
            <tr>
                <th>Volunteer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Skills</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="volunteersTableBody">
            <!-- Rows of volunteers will be added here -->
        </tbody>
    </table>

    <div id="volunteerFormContainer" style="display: none">
        <h2 id="formTitle"></h2>
        <form id="volunteerForm">
            <input type="hidden" id="volunteerId" name="volunteerId" />
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required />

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required />

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required />

            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills"></textarea>

            <label for="status">Status:</label>
            <input type="text" id="status" name="status" />

            <button type="submit" id="formSubmitButton">Create</button>
        </form>
    </div>

    <script src="../../js/volunteers.js"></script>
</body>
</html>
