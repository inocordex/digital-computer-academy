# Digital Computer Academy - README

Welcome to the **Digital Computer Academy** repository! This project is a comprehensive web-based academic management system tailored for institutions to streamline administrative tasks, manage student and teacher data, and generate performance reports. It incorporates various roles and functionalities to provide an efficient and user-friendly experience.

## Features

### **Admin Roles**
1. **View Teachers and Students**  
   - Access lists of all enrolled teachers and students.  
   - Filter students by course.  
   - View detailed performance and contact information.

2. **Enroll Teachers and Students**  
   - Admins can register teachers and students into the system.  
   - Automatic ID generation for teachers and students.  

3. **Class Management**  
   - Update class links and schedules.  
   - Assign class links to specific courses.  

4. **Performance Reports**  
   - Generate performance reports based on student results.  
   - Calculate total marks and grades dynamically.  
   - Display a performance summary in tabular form.

5. **Lot Performance Graphs**  
   - Visualize course-wise average performance using bar graphs.  
   - Monitor overall academic progress trends.

---

## Database Structure

### **Tables**
1. **`users` Table**  
   Stores student details and their academic performance:
   - Fields: `registration_number`, `child_name`, `course`, `cat1`, `cat2`, `cat3`, `total_marks`, `grade`.

2. **`teachers` Table**  
   Stores teacher details:
   - Fields: `unique_id`, `other_names`, `course`, `phone_number`.

3. **`settings` Table**  
   Stores global settings like class links and schedules:
   - Fields: `id`, `class_link`, `class_start_time`.

---

## Technologies Used

- **Frontend**: HTML, CSS (Bootstrap for responsiveness)
- **Backend**: PHP (session handling, database interactions)
- **Database**: MySQL
- **Charting**: Chart.js for data visualization
- **Version Control**: GitHub repository

---

## Installation and Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/inocordex/digital-computer-academy.git
   ```
2. Set up the database:
   - Import the SQL script provided in the `database` folder into your MySQL server.
3. Update the configuration:
   - Edit the `includes/config.php` file to match your database credentials.
4. Start the application:
   - Open the project in your browser via a local server (e.g., XAMPP, WAMP).

---

## How to Use

1. **Admin Login**  
   - Navigate to `admin_login.php` to access the admin panel.  
   - Manage teachers, students, and system settings.

2. **Enroll Users**  
   - Add new teachers via `enroll_teacher.php`.  
   - Add new students via `register.php`.

3. **Generate Reports**  
   - View and generate detailed performance reports using `generate_reports.php`.  
   - Analyze performance trends using dynamic graphs.

4. **Update Class Links**  
   - Modify and assign class schedules in the admin panel.

---

## Contributing

We welcome contributions to enhance the functionality and user experience of the Digital Computer Academy. Please follow these steps to contribute:

1. Fork the repository.
2. Create a feature branch:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Feature: Add new functionality"
   ```
4. Push the changes:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request.

---

## License

This project is open-source and available under the [MIT License](LICENSE).

---

## Contact

For any inquiries or support, please contact:
- **Email**: isacknewton313@gmail.com  
- **Location**: Kenya  
