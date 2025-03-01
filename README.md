# Tasks: A Simple PHP Task Management Application

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E=7.4-blue)](https://www.php.net/)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5-purple)](https://getbootstrap.com/)

Tasks is a basic, file-based task management application built with PHP, Bootstrap 5, and Tabler Icons. It allows users to create projects, organize tasks into sections, and mark tasks as complete. This project is designed to be simple to set up and use, making it ideal for small teams or personal use.

## Features

*   **Project Management:** Create, view, and delete projects.
*   **Section Organization:** Organize tasks within projects using sections.
*   **Task Management:** Add, view, delete, and mark tasks as complete.
*   **Search:** Quickly find projects, sections, or tasks using the built-in search functionality.
*   **User-Friendly Interface:** Clean and intuitive design using Bootstrap 5 and Tabler Icons.
*   **File-Based Storage:** No database required; data is stored in a simple JSON file.

## Installation

1.  **Clone the Repository:**

    ```bash
    git clone https://github.com/your-username/tasks.git
    cd tasks/src
    ```

2.  **Configure PHP:**

    *   Ensure you have PHP 7.4 or higher installed.
    *   Verify that the `json` extension is enabled in your `php.ini` file.

3.  **Configure `config.php`:**
    *   Modify the `$file` variable to point to the desired location for your data file (e.g., `data.json`).  Make sure the web server has write permissions to this file.

    ```php
    <?php
    // config.php

    $file = "data.json"; // Path to your data file
    ?>
    ```

4.  **Set up Web Server:**

    *   Configure your web server (e.g., Apache, Nginx) to serve the `src` directory.
    *   Make sure PHP is properly configured to handle `.php` files.

5.  **Access the Application:**

    *   Open your web browser and navigate to the URL where you've set up the application (e.g., `http://localhost/tasks/src/`).

## Usage

*   **Creating a Project:** Click the "+" button to create a new project. Enter the project name and click "Create Project."
*   **Viewing a Project:** Click the "View Project" button on a project card to see its sections.
*   **Creating a Section:** Click the "+" button within a project to create a new section. Enter the section name and click "Create Section."
*   **Viewing a Section:** Click the "View Section" button on a section card to see its tasks.
*   **Creating a Task:** Click the "+" button within a section to create a new task. Enter the task name and click "Create Task."
*   **Marking a Task as Complete:** Check the checkbox next to a task to mark it as complete.
*   **Deleting a Project, Section, or Task:** Click the trash icon to delete the item. You'll be prompted to confirm the deletion.
*   **Searching:** Use the search bars on the project list, project view, and section view to quickly find items.

## Technologies Used

*   **PHP:** Server-side scripting language
*   **Bootstrap 5:** CSS framework for responsive design
*   **Tabler Icons:** High-quality open-source icons
*   **JSON:** Data storage format

## Contributing

Contributions are welcome! Please feel free to submit pull requests with bug fixes, new features, or improvements to the documentation.

1.  Fork the repository.
2.  Create a new branch for your feature or bug fix.
3.  Make your changes and commit them with descriptive commit messages.
4.  Submit a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

*   [Bootstrap](https://getbootstrap.com/) for providing a fantastic CSS framework.
*   [Tabler Icons](https://tabler-icons.io/) for the beautiful and versatile icons.
