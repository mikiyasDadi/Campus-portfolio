# PROJECT DOCUMENTATION: ACADEMIC SCHEDULING SYSTEM

## **TABLE OF CONTENTS**
1. [Chapter One: Introduction](#chapter-one-introduction)
2. [Chapter Two: Naming, Coding Standards and Coding Process](#chapter-two-naming-coding-standards-and-coding-process)
3. [Chapter Three: Testing Process](#chapter-three-testing-process)
4. [Chapter Four: Security Design & Implementation](#chapter-four-security-design--implementation)
5. [Chapter Five: System Deployment Strategy](#chapter-five-system-deployment-strategy)
6. [Chapter Six: System Maintenance Strategy](#chapter-six-system-maintenance-strategy)
7. [Chapter Seven: Conclusion and Recommendation](#chapter-seven-conclusion-and-recommendation)
8. [References](#references)
9. [Appendix](#appendix)

---

## **CHAPTER FOUR: SECURITY DESIGN & IMPLEMENTATION**

### **4.1 Database Level Security**
Security begins at the data layer to ensure integrity and confidentiality.
- **Relational Constraints**: Foreign key constraints prevent "orphan" records (e.g., a schedule cannot exist without a valid department).
- **Role-Based Scoping**: All database queries for schedules and users are automatically scoped by the user's `department_id` or `faculty_id`, ensuring a multi-tenant environment where departments cannot see each other's data.
- **Password Hashing**: All user passwords are encrypted using the **Bcrypt** algorithm before being stored, preventing exposure even in the event of a database breach.

### **4.2 System Level Security**
The application implements multiple defensive layers:
- **Middleware Authorization**: Custom Laravel middleware (`auth`, `role`) intercepts every request to verify the user's permissions before allowing access to specific modules (e.g., Only Admin can access the "Users" module).
- **CSRF Protection**: All forms include a unique Cross-Site Request Forgery token, protecting the system against malicious external form submissions.
- **Input Sanitization**: All data from CSV imports and form fields are validated and sanitized to prevent SQL Injection and Cross-Site Scripting (XSS).
- **Secure Sessions**: Session data is stored securely on the server side, with client-side cookies being encrypted and signed.

---

## **CHAPTER FIVE: SYSTEM DEPLOYMENT STRATEGY**

### **5.1 Deployment Strategies**
The system is designed for a **Cloud-First Deployment** on a LAMP (Linux, Apache, MySQL, PHP) stack.
- **Environment Management**: Use of `.env` files to separate production credentials from development code.
- **Automated Migrations**: The database schema is deployed using `php artisan migrate`, ensuring consistency across development, staging, and production environments.
- **Version Control**: The entire project is managed via **Git**, allowing for seamless updates and immediate rollbacks if necessary.

### **5.2 User Training Strategies**
To ensure successful adoption, a tiered training program was established:
- **Administrator Training**: Focus on organizational setup, faculty management, and global user imports.
- **Department Head Workshops**: Intensive sessions on the "Scheduling Portal," CSV validation, and interpreting engine warnings.
- **Self-Service Support**: Students and Instructors receive an intuitive, dashboard-centric interface with real-time "Happening Now" and "Next Class" indicators, requiring minimal formal training.

### **5.3 User Manual**
*(Summary of core operations)*
- **Admin**: Go to "Users" -> "Import Official Records" to sync student/faculty data.
- **Dept Head**: Navigate to "Scheduling Portal" -> "Upload Exam CSV" -> Click "Initiate Scheduler" -> "Save & Publish".
- **Instructor/Student**: Log in to view your dynamic horizontal timetable and the "Live" status of your current academic period.

### **5.4 Installation Strategies**
**Prerequisites**: PHP 8.4+, MySQL 8.0+, Composer.
1.  **Clone**: Download the repository from the central server.
2.  **Install**: Run `composer install` to pull all framework dependencies.
3.  **Configure**: Set up the `.env` file with local database credentials.
4.  **Database**: Run `php artisan migrate --seed` to create the structure and initial system roles.
5.  **Serve**: Configure the web server (Apache/Nginx) to point to the `public/` directory.

---

## **CHAPTER SIX: SYSTEM MAINTENANCE STRATEGY**

### **6.1 System Modification Strategy**
The system follows an **Evolutionary Maintenance** model.
- **Modular Updates**: The scheduling engines are decoupled from the controllers, allowing for algorithmic improvements (e.g., adding room equipment constraints) without redesigning the UI.
- **Feedback Loop**: The "Comments" feature between Faculty Heads and Department Heads serves as a built-in mechanism for identifying needed modifications or bug fixes in published schedules.

### **6.2 Backup and Recovery Strategy**
- **Automated Backups**: Weekly database dumps are generated and stored in a separate secure storage location.
- **Point-in-Time Recovery**: In the event of data corruption, the system can be restored to its last known stable state using the `restore_backup.sql` scripts provided in the `database/` directory.
- **Disaster Recovery**: The application can be redeployed on a new server in under 30 minutes by running the standard installation sequence and importing the latest DB dump.

---

## **CHAPTER SEVEN: CONCLUSION AND RECOMMENDATION**

### **7.1 Conclusion**
The Academic Scheduling System has successfully transformed a manual, error-prone process into a streamlined, automated operation. By implementing a backtracking-based engine with specialized "Jump" logic, we have achieved 100% conflict-free scheduling while significantly improving the student preparation experience. The project demonstrates the power of modern web frameworks (Laravel) and algorithmic search in solving complex institutional logistics.

### **7.2 Recommendations**
1.  **AI Optimization**: Integrate machine learning to predict optimal room sizes based on historical attendance.
2.  **Mobile Integration**: Develop native iOS/Android apps for push notifications when a schedule is updated or a comment is left by a Faculty Head.
3.  **LMS Sync**: Directly integrate with Learning Management Systems (like Moodle) to automatically enroll students into their scheduled course sections.

---

## **REFERENCES**
- Laravel Documentation (2024). *The PHP Framework for Web Artisans.*
- Russell, S., & Norvig, P. (2020). *Artificial Intelligence: A Modern Approach.*
- MySQL Reference Manual (2024). *Relational Database Design.*

---

## **APPENDIX**
- **Appendix A**: Database Schema Diagram.
- **Appendix B**: Backtracking Engine Pseudocode.
- **Appendix C**: User Role Matrix.

## **CHAPTER ONE: INTRODUCTION**

### **1.1 Background of the Implementation**
The Academic Scheduling System was developed to modernize the complex task of creating and managing academic timetables. In many institutions, scheduling is still performed manually or with inadequate tools, leading to frequent conflicts, resource underutilization, and administrative stress. This implementation aims to provide an automated, conflict-free, and user-friendly platform for administrators, faculty, and students.

The system addresses critical pain points such as:
- **Conflict Resolution**: Ensuring no instructor or room is double-booked.
- **Student Preparation**: Implementing mandatory gaps ("Jumps") between exams to allow students adequate study time.
- **Data Accessibility**: Providing real-time dashboards for all stakeholders.
- **Hierarchical Management**: Supporting the organizational structure of Faculty -> Department -> Section.

### **1.2 Methods of Implementation**
The system was implemented using a **Phased Approach Strategy**, combined with elements of the **Parallel Strategy** during the transition phase.

- **Phased Approach**: Modules were developed and deployed sequentially—starting with core user management, followed by class scheduling, and finally the complex exam scheduling engine. This ensured that each core component was stable before adding further complexity.
- **Pilot Strategy**: The system was first tested with a single department's data to verify the backtracking engine's performance and accuracy under real-world constraints.
- **Direct Cutover**: Once verified, the legacy manual processes were replaced by the automated system to ensure a single source of truth for all scheduling data.

---

## **CHAPTER TWO: NAMING, CODING STANDARDS AND CODING PROCESS**

### **2.1 Algorithms**
The "intelligence" of the system is built on advanced search and constraint satisfaction algorithms.

#### **A. Backtracking Algorithm (Class & Exam Engines)**
The system utilizes a **Backtracking Search** to find conflict-free schedules. 
- **Mechanism**: The engine treats scheduling as a Constraint Satisfaction Problem (CSP). It builds the schedule incrementally, and if it reaches a state where no valid assignment can be made for the next course, it "backtracks" to the previous step and tries a different path.
- **Pruning**: To handle large datasets, the engine uses heuristics like "Most Constrained Variable" (scheduling courses with the most exclusion rules first) to prune the search tree early.

#### **B. Sequential Jump Heuristic (Exam Logic)**
A specialized heuristic was developed for the Exam Engine to enforce a one-day gap between exams for the same student group. This algorithm converts calendar dates into sequence numbers, ensuring that if an exam is on Day $N$, the next exam for that group is restricted to Day $N+2$ or later.

### **2.2 Coding Standards**
To ensure code quality and collaborative ease, the project strictly follows industry-standard conventions.

- **File Naming**: 
    - **Classes/Controllers**: PascalCase (e.g., `ExamSchedulerController.php`).
    - **Views/Templates**: lowercase_snake_case (e.g., `locked_schedules.blade.php`).
    - **Migrations**: timestamped_snake_case (e.g., `2024_05_23_create_exam_schedules_table.php`).
- **Variable Declarations**: 
    - camelCase for PHP variables and method names (e.g., `$processedData`, `initiateEngine()`).
    - snake_case for database columns and array keys (e.g., `department_id`, `start_date`).
- **Comments**: 
    - PHPDoc headers for all classes and methods to define parameters and return types.
    - Inline comments for complex algorithmic blocks within the `SchedulingEngine`.

### **2.3 Coding Process**
The development followed an **Incremental Coding Strategy** within an **Agile Framework**.

1.  **Requirement Analysis**: Translating academic policies (like the "1-day jump" rule) into technical constraints.
2.  **Database Design**: Creating a relational schema that supports hierarchical filtering (Faculty -> Dept -> User).
3.  **Core Logic Development**: Writing the service-layer engines in isolation from the UI.
4.  **Integration & UI**: Building the Blade templates and linking them to the backend controllers.
5.  **Refinement (Pair Programming)**: Continuous iteration based on user feedback, such as refining the horizontal grid layout to minimize scrolling.

---

## **CHAPTER THREE: TESTING PROCESS**

### **3.1 Test Plan**
The test plan was designed to verify both the structural integrity of the code and the functional accuracy of the scheduling engines.

**Primary Goals**:
- Validate that the `isSafe()` method correctly detects 100% of room and instructor conflicts.
- Ensure the "Jump" logic is enforced across week boundaries (Friday to Monday gaps).
- Verify that role-based access control (RBAC) prevents students from accessing administrative routes.

### **3.2 Test Case Design**
Test cases were categorized into **Functional**, **Boundary**, and **Negative** tests.

| Test ID | Scenario | Input Data | Expected Result |
| :--- | :--- | :--- | :--- |
| TC-01 | Room Conflict | Two exams in the same room/period. | Engine rejects the second assignment. |
| TC-02 | Jump Logic | Exam on Day 1 (Mon), next on Day 2 (Tue). | Engine blocks Day 2 and forces a move to Day 3. |
| TC-03 | Role Security | Student ID 5 accessing `/admin`. | System redirects to dashboard with 403 error. |
| TC-04 | CSV Integrity | CSV with invalid course code. | System flags the specific row as "CRITICAL ERROR". |

### **3.3 Test Procedures**
1.  **Unit Testing**: Isolated testing of engine methods using mock data.
2.  **Integration Testing**: Verifying the flow from CSV Upload -> Engine Processing -> Database Save.
3.  **System Testing**: Running the complete scheduling cycle for an entire department (30+ courses) to ensure no resource starvation.
4.  **Acceptance Testing**: Validating the output grid against manual schedules provided by the department heads.

---
