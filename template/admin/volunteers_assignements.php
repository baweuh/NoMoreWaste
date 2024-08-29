<!DOCTYPE html>
<html lang="en" data-default-lang="">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Volunteer Assignments</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <h1>Manage Volunteer Assignments</h1>
    <button onclick="showAssignmentCreateForm()">Create New Assignment</button>
    <table id="assignmentsTable">
      <thead>
        <tr>
          <th>Assignment ID</th>
          <th>Volunteer ID</th>
          <th>Service ID</th>
          <th>Task</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="assignmentsTableBody">
        <!-- Rows of assignments will be added here -->
      </tbody>
    </table>

    <div id="assignmentFormContainer" style="display: none">
      <h2 id="formTitle"></h2>
      <form id="assignmentForm">
        <input type="hidden" id="assignmentId" name="assignmentId" />

        <label for="volunteer_id">Volunteer ID:</label>
        <select id="volunteer_id" name="volunteer_id" required>
          <!-- Options will be populated by JavaScript -->
        </select>

        <label for="service_id">Service ID:</label>
        <select id="service_id" name="service_id" required>
          <!-- Options will be populated by JavaScript -->
        </select>

        <label for="task">Task:</label>
        <input type="text" id="task" name="task" required />

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required />

        <label for="status">Status:</label>
        <input type="text" id="status" name="status" />

        <button type="submit" id="formSubmitButton">Create</button>
      </form>
    </div>

    <script src="../../js/assignements.js"></script>
  </body>
</html>
